<?php

namespace App\Authentication\Domain\Exception;

class InvalidCredentialsException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Invalid credentials');
    }
}
