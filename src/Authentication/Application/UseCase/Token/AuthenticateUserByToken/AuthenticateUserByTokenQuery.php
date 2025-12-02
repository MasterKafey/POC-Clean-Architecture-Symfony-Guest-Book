<?php

namespace App\Authentication\Application\UseCase\Token\AuthenticateUserByToken;

use App\Authentication\Domain\ValueObject\TokenValue;

final readonly class AuthenticateUserByTokenQuery
{
    public function __construct(
        private TokenValue $value
    )
    {

    }

    public function getValue(): TokenValue
    {
        return $this->value;
    }
}
