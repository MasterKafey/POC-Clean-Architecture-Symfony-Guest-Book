<?php

namespace App\Authentication\Application\UseCase\User\GetUsers;

use App\Authentication\Domain\Repository\UserRepositoryInterface;

final readonly class GetUsersHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    )
    {

    }

    public function __invoke(
        GetUsersQuery $query,
    ): GetUsersResult
    {
        $users = $this->userRepository->findAll($query->getPagination());

        return new GetUsersResult($users);
    }
}
