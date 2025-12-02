<?php

namespace App\Authentication\Domain\Exception;

use App\Authentication\Domain\Entity\User;

class UserNotValidatedException extends DomainException
{
    public function __construct(
        private readonly User $user
    )
    {
        parent::__construct(sprintf("User '%s' is not validated.", $user->getEmail()));
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
