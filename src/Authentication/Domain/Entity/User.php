<?php

namespace App\Authentication\Domain\Entity;

use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\FirstName;
use App\Authentication\Domain\ValueObject\LastName;
use App\Authentication\Domain\ValueObject\PasswordHash;
use App\Authentication\Domain\ValueObject\UserId;

final class User
{
    public function __construct(
        private readonly UserId       $id,
        private readonly FirstName    $firstName,
        private readonly LastName     $lastName,
        private readonly Email        $email,
        private readonly PasswordHash $password,
        private bool                  $admin = false,
        private bool                  $banned = false,
        private bool                  $validated = false,
    )
    {
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getFirstName(): FirstName
    {
        return $this->firstName;
    }

    public function getLastName(): LastName
    {
        return $this->lastName;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): PasswordHash
    {
        return $this->password;
    }

    public function admin(): bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $isAdmin): self
    {
        $this->admin = $isAdmin;
        return $this;
    }

    public function banned(): bool
    {
        return $this->banned;
    }

    public function ban(): self
    {
        $this->banned = true;
        return $this;
    }

    public function unban(): self
    {
        $this->banned = false;
        return $this;
    }

    public function validated(): bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;
        return $this;
    }

    public function canConnect(): bool
    {
        return !$this->banned() && $this->validated();
    }
}
