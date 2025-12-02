<?php

namespace App\Comment\Application\UseCase\Comment\GetComments;

use App\Shared\Domain\ValueObject\Pagination;

final readonly class GetCommentsQuery
{
    public function __construct(
        private Pagination $pagination,
        private bool       $includeBlocked = false,
    )
    {

    }

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    public function getIncludeBlocked(): bool
    {
        return $this->includeBlocked;
    }
}
