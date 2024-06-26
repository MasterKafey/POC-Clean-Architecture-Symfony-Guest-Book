<?php

namespace App\Application\UseCase\GuestBookEntry\Create;

use App\Domain\Entity\GuestBookEntry;
use App\Domain\Repository\GuestBookEntryRepositoryInterface;

readonly class CreateGuestBookEntry
{
    public function __construct(
        private GuestBookEntryRepositoryInterface $repository,
    )
    {

    }

    public function execute(
        CreateGuestBookEntryRequest $request,
        CreateGuestBookEntryPresenterInterface $presenter
    ): void
    {
        // TODO: Add validation

        $entry = (new GuestBookEntry())
            ->setAuthor($request->getAuthor())
            ->setMessage($request->getMessage())
            ->setCreatedAt(new \DateTimeImmutable())
        ;

        $this->repository->save($entry);
        $presenter->present(new CreateGuestBookEntryResponse($entry));
    }
}