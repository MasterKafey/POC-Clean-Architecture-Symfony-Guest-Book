<?php

namespace App\Infrastructure\Controller;

use App\Application\UseCase\GuestBookEntry\Create\CreateGuestBookEntry;
use App\Application\UseCase\GuestBookEntry\Create\CreateGuestBookEntryPresenterInterface;
use App\Application\UseCase\GuestBookEntry\Create\CreateGuestBookEntryRequest;
use App\UserInterface\View\GuestBookEntry\Create\CreateGuestBookEntryJsonView;
use App\UserInterface\View\GuestBookEntry\Create\CreateGuestBookEntryViewInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/guest-book-entry',
    name: 'app_guest_book_entry_create',
    methods: [Request::METHOD_POST],
)]
class CreateGuestBookEntryController
{
    public function __invoke(
        CreateGuestBookEntry                   $createGuestBookEntry,
        CreateGuestBookEntryPresenterInterface $presenter,
        CreateGuestBookEntryJsonView           $view,
        Request                                $request
    )
    {
        $request = new CreateGuestBookEntryRequest(
            $request->toArray()['author'],
            $request->toArray()['message']
        );

        $createGuestBookEntry->execute($request, $presenter);

        return $view->generateResponse($presenter->getViewModel());
    }
}