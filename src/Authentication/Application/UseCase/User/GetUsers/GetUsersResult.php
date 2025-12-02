<?php

namespace App\Authentication\Application\UseCase\User\GetUsers;

final readonly class GetUsersResult
{
    public function __construct(
        private array $users,
    )
    {
    }

    public function getUsers(): array
    {
        return $this->users;
    }
}
