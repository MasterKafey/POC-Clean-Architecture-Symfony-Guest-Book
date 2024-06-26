<?php

namespace App\Application\UseCase\GuestBookEntry\Create;

interface CreateGuestBookEntryPresenterInterface
{
    public function present(CreateGuestBookEntryResponse $response): void;

    public function getViewModel(): CreateGuestBookEntryResponse;
}