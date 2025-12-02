<?php

namespace App\Authentication\Infrastructure\Symfony\Controller;

use App\Authentication\Application\UseCase\User\BanUser\BanUserCommand;
use App\Authentication\Application\UseCase\User\BanUser\BanUserHandler;
use App\Authentication\Application\UseCase\User\GetUsers\GetUsersHandler;
use App\Authentication\Application\UseCase\User\GetUsers\GetUsersQuery;
use App\Authentication\Application\UseCase\User\RegisterUser\RegisterUserCommand;
use App\Authentication\Application\UseCase\User\RegisterUser\RegisterUserHandler;
use App\Authentication\Application\UseCase\User\UnbanUser\UnbanUserCommand;
use App\Authentication\Application\UseCase\User\UnbanUser\UnbanUserHandler;
use App\Authentication\Application\UseCase\User\ValidateEmail\ValidateEmailCommand;
use App\Authentication\Application\UseCase\User\ValidateEmail\ValidateEmailHandler;
use App\Authentication\Domain\Entity\User as UserDomain;
use App\Authentication\Domain\Port\PasswordHasherPort;
use App\Authentication\Domain\Repository\TokenRepositoryInterface;
use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\FirstName;
use App\Authentication\Domain\ValueObject\LastName;
use App\Authentication\Domain\ValueObject\TokenValue;
use App\Authentication\Domain\ValueObject\UserId;
use App\Authentication\Infrastructure\Doctrine\Adapter\Repository\DoctrineUserRepository;
use App\Authentication\Infrastructure\Doctrine\Entity\User as UserDoctrine;
use App\Authentication\Infrastructure\Symfony\Dto\Input\RegisterUserInput;
use App\Authentication\Infrastructure\Symfony\Dto\Output\UserOutput;
use App\Shared\Domain\Port\EventBusPort;
use App\Shared\Domain\Port\UuidGeneratorPort;
use App\Shared\Domain\ValueObject\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly DoctrineUserRepository $userRepository,
        private readonly UuidGeneratorPort      $uuidGenerator,
        private readonly PasswordHasherPort     $passwordHasher,
        private readonly EventBusPort           $eventBus, private readonly TokenRepositoryInterface $tokenRepository,
    )
    {

    }

    #[Route('/register')]
    public function register(#[MapRequestPayload] RegisterUserInput $input): JsonResponse
    {
        $command = new RegisterUserCommand(
            new FirstName($input->firstName),
            new LastName($input->lastName),
            new Email($input->email),
            $input->password
        );

        $handler = new RegisterUserHandler(
            $this->userRepository,
            $this->uuidGenerator,
            $this->passwordHasher,
            $this->eventBus,
        );
        $response = $handler($command);

        return $this->json([
            'user' => UserOutput::fromDomain($response->user())
        ]);
    }

    #[Route('/me')]
    #[IsGranted('ROLE_USER')]
    public function me(): JsonResponse
    {
        return $this->json([
            'identifier' => $this->getUser()->getUserIdentifier()
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route(methods: [Request::METHOD_GET])]
    public function getUsers(Request $request): JsonResponse
    {
        $query = new GetUsersQuery(
            new Pagination(
                $request->query->getInt('page', 1),
                $request->query->getInt('max', 20)
            )
        );

        $handler = new GetUsersHandler(
            $this->userRepository,
        );
        $result = $handler($query);

        return $this->json(array_map(function (UserDomain $user) {
            return UserOutput::fromDomain($user);
        }, $result->getUsers()));
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/ban', methods: [Request::METHOD_PUT])]
    public function ban(string $id): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof UserDoctrine) {
            throw new \LogicException();
        }

        $command = new BanUserCommand(
            new UserId($id),
            new UserId($user->getId())
        );
        $handler = new BanUserHandler(
            $this->userRepository,
            $this->eventBus,
        );
        $handler($command);

        return $this->json([
            'result' => true
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/unban', methods: [Request::METHOD_PUT])]
    public function unban(string $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof UserDoctrine) {
            throw new \LogicException();
        }

        $command = new UnbanUserCommand(new UserId($id), new UserId($user->getId()));
        $handler = new UnbanUserHandler($this->userRepository);
        $handler($command);

        return $this->json([
            'result' => true
        ]);
    }

    #[Route('/validate-email/{token}', name: 'app_authentication_infrastructure_symfony_user_validate_email', methods: [Request::METHOD_PATCH])]
    public function validateEmail(
        string $token
    ): JsonResponse
    {
        $command = new ValidateEmailCommand(new TokenValue($token));
        $handler = new ValidateEmailHandler(
            $this->userRepository,
            $this->tokenRepository
        );
        $handler($command);

        return $this->json([
            'result' => true
        ]);
    }
}
