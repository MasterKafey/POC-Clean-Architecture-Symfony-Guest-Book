<?php

namespace App\Authentication\Application\UseCase\User\RegisterUser;

use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\FirstName;
use App\Authentication\Domain\ValueObject\LastName;

final readonly class RegisterUserCommand
{
    public function __construct(
        private FirstName $firstName,
        private LastName  $lastName,
        private Email     $email,
        private string    $plainPassword
    )
    {
    }

    public function firstName(): FirstName
    {
        return $this->firstName;
    }

    public function lastName(): LastName
    {
        return $this->lastName;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function plainPassword(): string
    {
        return $this->plainPassword;
    }
}
