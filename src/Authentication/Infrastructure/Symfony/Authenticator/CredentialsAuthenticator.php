<?php

namespace App\Authentication\Infrastructure\Symfony\Authenticator;

use App\Authentication\Application\UseCase\Token\CreateToken\CreateTokenCommand;
use App\Authentication\Application\UseCase\Token\CreateToken\CreateTokenHandler;
use App\Authentication\Application\UseCase\User\AuthenticateUser\AuthenticateUserHandler;
use App\Authentication\Application\UseCase\User\AuthenticateUser\AuthenticateUserQuery;
use App\Authentication\Domain\Enum\TokenType;
use App\Authentication\Domain\Exception\DomainException;
use App\Authentication\Domain\Port\PasswordHasherPort;
use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\UserId;
use App\Authentication\Infrastructure\Doctrine\Adapter\Repository\DoctrineTokenRepository;
use App\Authentication\Infrastructure\Doctrine\Adapter\Repository\DoctrineUserRepository;
use App\Authentication\Infrastructure\Doctrine\Entity\User;
use App\Authentication\Infrastructure\Symfony\Dto\Input\LoginUserInput;
use App\Shared\Domain\Port\EventBusPort;
use App\Shared\Domain\Port\UuidGeneratorPort;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CredentialsAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly DoctrineTokenRepository $tokenRepository,
        private readonly DoctrineUserRepository  $userRepository,
        private readonly UuidGeneratorPort       $uuidGenerator,
        private readonly ValidatorInterface      $validator,
        private readonly PasswordHasherPort      $passwordHasher,
        private readonly EntityManagerInterface  $entityManager,
        private readonly EventBusPort            $eventBus
    )
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'app_user_login' && $request->isMethod(Request::METHOD_POST);
    }

    public function authenticate(Request $request): Passport
    {
        $input = LoginUserInput::fromRequest($request);

        $errors = $this->validator->validate($input);

        if (count($errors) > 0) {
            throw new AuthenticationException((string)$errors);
        }

        $query = new AuthenticateUserQuery(new Email($input->email), $input->password);
        $handler = new AuthenticateUserHandler(
            $this->userRepository,
            $this->passwordHasher,
            $this->eventBus,
        );

        try {
            $result = $handler($query);
        } catch (DomainException $exception) {
            throw new AuthenticationException($exception->getMessage(), 0, $exception);
        }

        return new SelfValidatingPassport(
            new UserBadge(
                $result->getUser()->getId(),
                function (string $id) {
                    return $this->entityManager->getRepository(User::class)->find($id);
                }
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            throw new \LogicException();
        }

        $command = new CreateTokenCommand(
            new UserId($user->getId()),
            TokenType::AUTHENTICATION,
        );

        $handler = new CreateTokenHandler(
            $this->tokenRepository,
            $this->userRepository,
            $this->uuidGenerator
        );

        $token = $handler($command);

        return new JsonResponse([
            'token' => $token->getValue()->value(),
        ]);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error' => $exception->getMessage(),
        ]);
    }
}
