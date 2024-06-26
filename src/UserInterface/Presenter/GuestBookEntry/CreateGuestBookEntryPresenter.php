<?php

namespace App\UserInterface\Presenter\GuestBookEntry;

use App\Application\UseCase\GuestBookEntry\Create\CreateGuestBookEntryPresenterInterface;
use App\Application\UseCase\GuestBookEntry\Create\CreateGuestBookEntryResponse;

class CreateGuestBookEntryPresenter implements CreateGuestBookEntryPresenterInterface
{
    private CreateGuestBookEntryResponse $response;

    public function present(CreateGuestBookEntryResponse $response): void
    {
        $this->response = $response;
    }

    public function getViewModel(): CreateGuestBookEntryResponse
    {
        return $this->response;
    }
}