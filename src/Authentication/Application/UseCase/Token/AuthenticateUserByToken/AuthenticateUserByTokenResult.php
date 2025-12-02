<?php

namespace App\Authentication\Application\UseCase\Token\AuthenticateUserByToken;

use App\Authentication\Domain\Entity\User;

final readonly class AuthenticateUserByTokenResult
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
