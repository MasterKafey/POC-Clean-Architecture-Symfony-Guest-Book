<?php

namespace App\Authentication\Domain\Exception;

class TokenExpiredException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Token expired.');
    }
}
