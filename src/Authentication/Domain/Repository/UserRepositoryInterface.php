<?php

namespace App\Authentication\Domain\Repository;

use App\Authentication\Domain\Entity\User;
use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\Pagination;

interface UserRepositoryInterface
{
    public function save(User $user): User;

    public function findByEmail(Email $email): ?User;

    public function findById(UserId $getUserId): ?User;

    /** @return User[] */
    public function findAll(Pagination $pagination): array;

}
