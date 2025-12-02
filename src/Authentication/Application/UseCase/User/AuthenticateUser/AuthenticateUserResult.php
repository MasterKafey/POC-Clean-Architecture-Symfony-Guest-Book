<?php

namespace App\Authentication\Application\UseCase\User\AuthenticateUser;

use App\Authentication\Domain\Entity\User;

readonly class AuthenticateUserResult
{

    public function __construct(
        private User $user
    )
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
