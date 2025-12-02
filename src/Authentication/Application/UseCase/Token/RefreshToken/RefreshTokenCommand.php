<?php

namespace App\Authentication\Application\UseCase\Token\RefreshToken;

use App\Authentication\Domain\ValueObject\TokenValue;

final readonly class RefreshTokenCommand
{
    public function __construct(
        private TokenValue $tokenValue
    )
    {

    }

    public function getTokenValue(): TokenValue
    {
        return $this->tokenValue;
    }
}
