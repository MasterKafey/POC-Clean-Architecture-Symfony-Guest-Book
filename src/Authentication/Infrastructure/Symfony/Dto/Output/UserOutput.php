<?php

namespace App\Authentication\Infrastructure\Symfony\Dto\Output;

use App\Authentication\Domain\Entity\User;

final readonly class UserOutput
{
    public function __construct(
        private string $id,
        private string $email,
        private string $firstName,
        private string $lastName,
        private bool   $banned,
    )
    {

    }

    public static function fromDomain(User $user): self
    {
        return new self(
            $user->getId(),
            $user->getEmail(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->banned()
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isBanned(): bool
    {
        return $this->banned;
    }
}
