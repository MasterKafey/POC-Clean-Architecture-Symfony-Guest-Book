<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\GuestBookEntry as DomainGuestBookEntry;
use App\Domain\Repository\GuestBookEntryRepositoryInterface;
use App\Infrastructure\Doctrine\Entity\GuestBookEntry as DoctrineGuestBookEntry;
use Doctrine\ORM\EntityManagerInterface;

readonly class GuestBookEntryRepository implements GuestBookEntryRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {

    }

    public function save(DomainGuestBookEntry $entry): void
    {
        $entity = (new DoctrineGuestBookEntry())
            ->setAuthor($entry->getAuthor())
            ->setMessage($entry->getMessage())
            ->setCreatedAt($entry->getCreatedAt());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $entry->setId($entity->getId());
    }

    public function all(int $currentPage, int $maxResult): array
    {
        $queryBuilder = $this->entityManager->getRepository(DoctrineGuestBookEntry::class)->createQueryBuilder('guest_book_entry');

        $queryBuilder
            ->setFirstResult($maxResult * ($currentPage - 1))
            ->setMaxResults($maxResult);

        $doctrineEntries = $queryBuilder->getQuery()->getResult();

        return array_map(
            fn(DoctrineGuestBookEntry $entry) => (new DomainGuestBookEntry())
                ->setId($entry->getId())
                ->setAuthor($entry->getAuthor())
                ->setMessage($entry->getMessage())
                ->setCreatedAt($entry->getCreatedAt()),
            $doctrineEntries
        );
    }
}