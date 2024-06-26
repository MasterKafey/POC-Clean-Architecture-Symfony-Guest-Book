<?php

namespace App\Application\UseCase\GuestBookEntry\Create;

use App\Domain\Entity\GuestBookEntry;

readonly class CreateGuestBookEntryResponse
{
    public function __construct(
        private readonly GuestBookEntry $guestBookEntry,
    )
    {

    }

    public function getGuestBookEntry(): GuestBookEntry
    {
        return $this->guestBookEntry;
    }
}