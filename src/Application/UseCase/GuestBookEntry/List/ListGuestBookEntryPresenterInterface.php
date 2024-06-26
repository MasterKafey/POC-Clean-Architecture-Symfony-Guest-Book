<?php

namespace App\Application\UseCase\GuestBookEntry\List;

interface ListGuestBookEntryPresenterInterface
{
    public function present(ListGuestBookEntryResponse $response): void;

    public function getViewModel(): ListGuestBookEntryResponse;
}