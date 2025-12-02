<?php

namespace App\Authentication\Application\UseCase\User\AuthenticateUser;

use App\Authentication\Domain\ValueObject\Email;

readonly class AuthenticateUserQuery
{
    public function __construct(
        private Email $email,
        private string $password,
    )
    {
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
