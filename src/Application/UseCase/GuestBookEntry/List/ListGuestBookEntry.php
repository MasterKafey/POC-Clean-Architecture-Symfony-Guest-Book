<?php

namespace App\Application\UseCase\GuestBookEntry\List;

use App\Domain\Repository\GuestBookEntryRepositoryInterface;

readonly class ListGuestBookEntry
{
    public function __construct(
        private readonly GuestBookEntryRepositoryInterface $repository
    )
    {

    }

    public function execute(
        ListGuestBookEntryRequest            $request,
        ListGuestBookEntryPresenterInterface $presenter
    )
    {
        $entries = $this->repository->all(
            max($request->getCurrentPage(), 1),
            min(max($request->getMaxResult(), 1), 500)
        );

        $presenter->present(new ListGuestBookEntryResponse($entries));
    }
}