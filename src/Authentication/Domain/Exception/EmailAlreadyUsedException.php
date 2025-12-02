<?php

namespace App\Authentication\Domain\Exception;

use App\Authentication\Domain\ValueObject\Email;

class EmailAlreadyUsedException extends \DomainException
{
    public function __construct(Email $email)
    {
        parent::__construct(sprintf("Email '%s' is already used", $email));
    }
}
