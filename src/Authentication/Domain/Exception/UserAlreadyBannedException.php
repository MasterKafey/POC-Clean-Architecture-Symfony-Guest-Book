<?php

namespace App\Authentication\Domain\Exception;

use App\Authentication\Domain\Entity\User;

class UserAlreadyBannedException extends DomainException
{
    public function __construct(User $user)
    {
        parent::__construct(sprintf("User '%s' already banned", $user->getEmail()));
    }
}
