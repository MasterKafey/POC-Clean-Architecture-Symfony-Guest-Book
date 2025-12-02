<?php

namespace App\Authentication\Domain\Exception;

use App\Authentication\Domain\Entity\User;

class UserBannedException extends DomainException
{
    public function __construct(User $user)
    {
        parent::__construct(sprintf("User '%s' is banned", $user->getEmail()));
    }
}
