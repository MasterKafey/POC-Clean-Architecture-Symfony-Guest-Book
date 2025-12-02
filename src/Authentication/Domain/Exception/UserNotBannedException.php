<?php

namespace App\Authentication\Domain\Exception;

use App\Authentication\Domain\Entity\User;

class UserNotBannedException extends DomainException
{
    public function __construct(User $user)
    {
        parent::__construct(sprintf("User '%s' not banned", $user->getEmail()));
    }
}
