<?php

namespace App\Authentication\Domain\Exception;

use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\UserId;

class UserNotFoundException extends DomainException
{
    public function __construct(Email|UserId $identifiant)
    {
        parent::__construct(sprintf("User not found with %s : '%s'", $identifiant instanceof Email ? 'email' : 'id', $identifiant));
    }
}
