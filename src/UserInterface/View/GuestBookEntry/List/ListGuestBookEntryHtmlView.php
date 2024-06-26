<?php

namespace App\UserInterface\View\GuestBookEntry\List;

use App\Application\UseCase\GuestBookEntry\List\ListGuestBookEntryResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

readonly class ListGuestBookEntryHtmlView implements ListGuestBookEntryViewInterface
{
    public function __construct(
        private Environment $environment
    )
    {
    }

    public function generateResponse(ListGuestBookEntryResponse $response): Response
    {
        return new Response($this->environment->render('Page/GuestBookEntry/list.html.twig', [
            'viewModel' => $response,
        ]));
    }
}