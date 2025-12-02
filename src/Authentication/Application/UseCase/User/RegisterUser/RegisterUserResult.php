<?php

namespace App\Authentication\Application\UseCase\User\RegisterUser;

use App\Authentication\Domain\Entity\User;
final readonly class RegisterUserResult
{
    public function __construct(
        private User $user,
    )
    {

    }

    public function user(): User
    {
        return $this->user;
    }
}
