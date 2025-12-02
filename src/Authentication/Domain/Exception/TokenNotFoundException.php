<?php

namespace App\Authentication\Domain\Exception;

use App\Authentication\Domain\ValueObject\TokenValue;

class TokenNotFoundException extends DomainException
{
    public function __construct(TokenValue $tokenValue)
    {
        parent::__construct(sprintf("Token with value '%s' not found", $tokenValue));
    }
}
