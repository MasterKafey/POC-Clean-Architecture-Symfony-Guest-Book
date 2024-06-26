<?php

namespace App\Domain\Repository;

use App\Domain\Entity\GuestBookEntry;

interface GuestBookEntryRepositoryInterface
{
    public function save(GuestBookEntry $entry): void;

    /** @return GuestBookEntry[] */
    public function all(int $currentPage, int $maxResult): array;
}