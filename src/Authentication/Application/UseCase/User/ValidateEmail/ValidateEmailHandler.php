<?php

namespace App\Authentication\Application\UseCase\User\ValidateEmail;

use App\Authentication\Domain\Enum\TokenType;
use App\Authentication\Domain\Exception\TokenExpiredException;
use App\Authentication\Domain\Exception\TokenNotFoundException;
use App\Authentication\Domain\Exception\UserAlreadyValidatedException;
use App\Authentication\Domain\Exception\UserBannedException;
use App\Authentication\Domain\Exception\UserNotFoundException;
use App\Authentication\Domain\Repository\TokenRepositoryInterface;
use App\Authentication\Domain\Repository\UserRepositoryInterface;

final readonly class ValidateEmailHandler
{
    public function __construct(
        private UserRepositoryInterface  $userRepository,
        private TokenRepositoryInterface $tokenRepository,
    )
    {
    }

    public function __invoke(
        ValidateEmailCommand $command
    ): void
    {
        $token = $this->tokenRepository->findTokenByValue($command->tokenValue());

        if (null === $token || $token->getTokenType() !== TokenType::REGISTRATION) {
            throw new TokenNotFoundException($command->tokenValue());
        }

        if ($token->isExpired()) {
            throw new TokenExpiredException();
        }

        $user = $this->userRepository->findById($token->getUserId());

        if (null === $user) {
            throw new UserNotFoundException($token->getUserId());
        }

        if ($user->banned()) {
            throw new UserBannedException($user);
        }

        if ($user->validated()) {
            throw new UserAlreadyValidatedException($user);
        }

        $user->setValidated(true);
        $this->userRepository->save($user);
        $this->tokenRepository->remove($token);
    }
}
