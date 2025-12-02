<?php

namespace App\Authentication\Application\UseCase\Token\AuthenticateUserByToken;

use App\Authentication\Domain\Entity\Token;
use App\Authentication\Domain\Enum\TokenType;
use App\Authentication\Domain\Event\UserAuthenticatedByToken;
use App\Authentication\Domain\Exception\TokenExpiredException;
use App\Authentication\Domain\Exception\UserBannedException;
use App\Authentication\Domain\Exception\UserNotFoundException;
use App\Authentication\Domain\Exception\UserNotValidatedException;
use App\Authentication\Domain\Repository\TokenRepositoryInterface;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Shared\Domain\Port\EventBusPort;

readonly class AuthenticateUserByTokenHandler
{
    public function __construct(
        private TokenRepositoryInterface $tokenRepository,
        private UserRepositoryInterface  $userRepository,
        private EventBusPort             $eventBus
    )
    {

    }

    public function __invoke(
        AuthenticateUserByTokenQuery $query
    ): AuthenticateUserByTokenResult
    {
        $token = $this->tokenRepository->findTokenByValue($query->getValue());

        if ($token === null || $token->isExpired() || $token->getTokenType() !== TokenType::AUTHENTICATION) {
            throw new TokenExpiredException();
        }

        $user = $this->userRepository->findById($token->getUserId());

        if ($user === null) {
            throw new UserNotFoundException($token->getUserId());
        }

        if ($user->banned()) {
            throw new UserBannedException($user);
        }

        if (!$user->validated()) {
            throw new UserNotValidatedException($user);
        }

        $this->eventBus->dispatch(new UserAuthenticatedByToken($user->getId(), $user->getEmail()));

        return new AuthenticateUserByTokenResult(
            $user,
        );
    }
}
