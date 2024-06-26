<?php

namespace App\UserInterface\Presenter\GuestBookEntry;

use App\Application\UseCase\GuestBookEntry\List\ListGuestBookEntryPresenterInterface;
use App\Application\UseCase\GuestBookEntry\List\ListGuestBookEntryResponse;

class ListGuestBookEntryPresenter implements ListGuestBookEntryPresenterInterface
{
    private ListGuestBookEntryResponse $response;

    public function present(ListGuestBookEntryResponse $response): void
    {
        $this->response = $response;
    }

    public function getViewModel(): ListGuestBookEntryResponse
    {
        return $this->response;
    }
}