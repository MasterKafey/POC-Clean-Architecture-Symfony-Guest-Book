<?php

namespace App\Infrastructure\Controller;

use App\Application\UseCase\GuestBookEntry\List\ListGuestBookEntry;
use App\Application\UseCase\GuestBookEntry\List\ListGuestBookEntryPresenterInterface;
use App\Application\UseCase\GuestBookEntry\List\ListGuestBookEntryRequest;
use App\UserInterface\View\GuestBookEntry\List\ListGuestBookEntryJsonView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/guest-book-entry',
    name: 'app_guest_book_entry_list',
    methods: [Request::METHOD_GET]
)]
class ListGuestBookEntryController
{
    public function __invoke(
        ListGuestBookEntry                   $listGuestBookEntry,
        ListGuestBookEntryPresenterInterface $presenter,
        ListGuestBookEntryJsonView           $view,
        Request                              $request
    ): Response
    {
        ['currentPage' => $currentPage, 'maxResult' => $maxResult] = array_merge(['currentPage' => 1, 'maxResult' => 10], $request->query->all());
        $request = new ListGuestBookEntryRequest($currentPage, $maxResult);

        $listGuestBookEntry->execute($request, $presenter);
        return $view->generateResponse($presenter->getViewModel());
    }
}