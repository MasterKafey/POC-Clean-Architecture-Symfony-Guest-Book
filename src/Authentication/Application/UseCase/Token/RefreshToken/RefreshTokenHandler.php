<?php

namespace App\Authentication\Application\UseCase\Token\RefreshToken;

use App\Authentication\Domain\Exception\TokenExpiredException;
use App\Authentication\Domain\Repository\TokenRepositoryInterface;

final readonly class RefreshTokenHandler
{
    public function __construct(
        private TokenRepositoryInterface $tokenRepository,
    )
    {

    }

    public function __invoke(
        RefreshTokenCommand $command
    ): void
    {
        $token = $this->tokenRepository->findTokenByValue($command->getTokenValue());

        if (null === $token) {
            throw new TokenExpiredException();
        }

        $token->refreshExpirationDate(((new \DateTimeImmutable())->modify('+15 minutes')));
        $this->tokenRepository->save($token);
    }
}
