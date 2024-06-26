<?php

namespace App\UserInterface\View\GuestBookEntry\Create;

use App\Application\UseCase\GuestBookEntry\Create\CreateGuestBookEntryResponse;
use Symfony\Component\HttpFoundation\Response;

interface CreateGuestBookEntryViewInterface {
    public function generateResponse(CreateGuestBookEntryResponse $response): Response;
}