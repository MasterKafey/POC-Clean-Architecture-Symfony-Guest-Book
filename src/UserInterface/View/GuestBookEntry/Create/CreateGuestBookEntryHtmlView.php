<?php

namespace App\UserInterface\View\GuestBookEntry\Create;

use App\Application\UseCase\GuestBookEntry\Create\CreateGuestBookEntryResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

readonly class CreateGuestBookEntryHtmlView implements CreateGuestBookEntryViewInterface
{
    public function __construct(
        private Environment $environment
    )
    {

    }

    public function generateResponse(CreateGuestBookEntryResponse $response): Response
    {
        return new Response($this->environment->render('Page/GuestBookEntry/create.html.twig', [
            'viewModel' => $response,
        ]));
    }
}