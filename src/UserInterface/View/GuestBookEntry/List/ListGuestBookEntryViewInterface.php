<?php

namespace App\UserInterface\View\GuestBookEntry\List;

use App\Application\UseCase\GuestBookEntry\List\ListGuestBookEntryResponse;
use Symfony\Component\HttpFoundation\Response;

interface ListGuestBookEntryViewInterface
{
    public function generateResponse(ListGuestBookEntryResponse $response): Response;
}