<?php

namespace App\Application\UseCase\GuestBookEntry\List;

readonly class ListGuestBookEntryRequest
{
    public function __construct(
        private int $currentPage,
        private int $maxResult,
    )
    {

    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getMaxResult(): int
    {
        return $this->maxResult;
    }
}