<?php

namespace App\Application\UseCase\GuestBookEntry\List;

readonly class ListGuestBookEntryResponse
{
    public function __construct(
        private array $entries,
    ) {

    }

    public function getEntries(): array
    {
        return $this->entries;
    }
}