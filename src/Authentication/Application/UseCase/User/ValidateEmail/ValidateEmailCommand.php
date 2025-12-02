<?php

namespace App\Authentication\Application\UseCase\User\ValidateEmail;

use App\Authentication\Domain\ValueObject\TokenValue;

final readonly class ValidateEmailCommand
{
    public function __construct(
        private TokenValue $tokenValue
    )
    {

    }

    public function tokenValue(): TokenValue
    {
        return $this->tokenValue;
    }
}
