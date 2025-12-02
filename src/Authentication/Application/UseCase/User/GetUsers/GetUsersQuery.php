<?php

namespace App\Authentication\Application\UseCase\User\GetUsers;

use App\Shared\Domain\ValueObject\Pagination;

final readonly class GetUsersQuery
{
    public function __construct(
        private Pagination $pagination
    )
    {

    }

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }
}
