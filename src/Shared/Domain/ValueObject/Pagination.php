<?php

namespace App\Shared\Domain\ValueObject;

final readonly class Pagination
{
    public function __construct(
        private int $currentPage,
        private int $maxResults,
    )
    {

    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getMaxResults(): int
    {
        return $this->maxResults;
    }
}
