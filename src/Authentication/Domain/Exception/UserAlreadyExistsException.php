<?php

namespace App\Authentication\Domain\Exception;

use App\Authentication\Domain\ValueObject\Email;

class UserAlreadyExistsException extends DomainException
{
    public function __construct(Email $email)
    {
        parent::__construct(sprintf('User with email "%s" already exists.', $email));
    }
}
