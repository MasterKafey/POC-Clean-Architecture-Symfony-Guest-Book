<?php

namespace App\Authentication\Infrastructure\Symfony\Authenticator;

use App\Authentication\Application\UseCase\Token\AuthenticateUserByToken\AuthenticateUserByTokenHandler;
use App\Authentication\Application\UseCase\Token\AuthenticateUserByToken\AuthenticateUserByTokenQuery;
use App\Authentication\Application\UseCase\Token\RefreshToken\RefreshTokenCommand;
use App\Authentication\Application\UseCase\Token\RefreshToken\RefreshTokenHandler;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\ValueObject\TokenValue;
use App\Authentication\Infrastructure\Doctrine\Adapter\Repository\DoctrineTokenRepository;
use App\Shared\Domain\Port\EventBusPort;
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

class TokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly DoctrineTokenRepository $tokenRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly EventBusPort            $eventBus
    )
    {
    }

    public function supports(Request $request): ?bool
    {
        return str_starts_with($request->headers->get('Authorization', ''), 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $tokenValue = new TokenValue(substr($request->headers->get('Authorization'), 7));

        $query = new AuthenticateUserByTokenQuery($tokenValue);
        $handler = new AuthenticateUserByTokenHandler(
            $this->tokenRepository,
            $this->userRepository,
            $this->eventBus,
        );
        $result = $handler($query);

        $query = new RefreshTokenCommand($tokenValue);
        $command = new RefreshTokenHandler(
            $this->tokenRepository,
        );
        $command($query);

        return new SelfValidatingPassport(
            new UserBadge(
                $result->getUser()->getEmail()
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error' => $exception->getMessage()
        ]);
    }
}
