<?php

namespace App\Comment\Infrastructure\Doctrine\Adapter;

use App\Authentication\Domain\Exception\UserNotFoundException;
use App\Authentication\Domain\ValueObject\UserId;
use App\Authentication\Infrastructure\Doctrine\Entity\User as UserDoctrine;
use App\Comment\Domain\Entity\Comment as CommentDomain;
use App\Comment\Domain\Exception\CommentNotFoundException;
use App\Comment\Infrastructure\Doctrine\Entity\Comment as CommentDoctrine;
use App\Comment\Domain\Repository\CommentRepositoryInterface;
use App\Comment\Domain\ValueObject\CommentId;
use App\Comment\Infrastructure\Symfony\Mapper\CommentDoctrineMapper;
use App\Shared\Domain\ValueObject\Pagination;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CommentRepository implements CommentRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function findAll(Pagination $pagination): array
    {
        $comments = $this->entityManager->getRepository(CommentDoctrine::class)->findBy(
            criteria: [],
            limit: $pagination->getMaxResults(),
            offset: ($pagination->getCurrentPage() - 1) * $pagination->getMaxResults()
        );

        return array_map(function (CommentDoctrine $commentDoctrine) {
            return CommentDoctrineMapper::toDomain($commentDoctrine);
        }, $comments);
    }

    public function findNotBlocked(Pagination $pagination): array
    {
        $comments = $this->entityManager->getRepository(CommentDoctrine::class)->findBy(
            criteria: ['blocked' => false],
            limit: $pagination->getMaxResults(),
            offset: ($pagination->getCurrentPage() - 1) * $pagination->getMaxResults()
        );

        return array_map(function (CommentDoctrine $commentDoctrine) {
            return CommentDoctrineMapper::toDomain($commentDoctrine);
        }, $comments);
    }

    public function findById(CommentId $id): ?CommentDomain
    {
        $commentDoctrine = $this->entityManager->getRepository(CommentDoctrine::class)->find($id->value());

        if (null === $commentDoctrine) {
            return null;
        }

        return CommentDoctrineMapper::toDomain($commentDoctrine);
    }

    public function findByUserId(UserId $userId): ?CommentDomain
    {
        $commentDoctrine = $this->entityManager->getRepository(CommentDoctrine::class)->findOneBy(['user' => $userId->value()]);

        if (null === $commentDoctrine) {
            return null;
        }

        return CommentDoctrineMapper::toDomain($commentDoctrine);
    }

    public function save(CommentDomain $commentDomain): CommentDomain
    {
        $commentDoctrine = $this->entityManager->getRepository(CommentDoctrine::class)->find($commentDomain->getId()->value());

        $userEntity = null;
        if (null === $commentDoctrine) {
            $commentDoctrine = new CommentDoctrine();
            $this->entityManager->persist($commentDoctrine);
        } else {
            $userEntity = $commentDoctrine->getUser();
        }

        if ($userEntity === null || $userEntity->getId() !== $commentDomain->getUserId()->value()) {
            $userEntity = $this->entityManager->getRepository(UserDoctrine::class)->find($commentDomain->getUserId()->value());
        }

        if (null === $userEntity) {
            throw new UserNotFoundException($commentDomain->getUserId());
        }

        CommentDoctrineMapper::toEntity($commentDomain, $commentDoctrine, $userEntity);
        $this->entityManager->flush();
        return CommentDoctrineMapper::toDomain($commentDoctrine);
    }

    public function delete(CommentId $id): void
    {
        $commentDoctrine = $this->entityManager->getRepository(CommentDoctrine::class)->find($id);

        if (null === $commentDoctrine) {
            throw new CommentNotFoundException($id);
        }

        $this->entityManager->remove($commentDoctrine);
        $this->entityManager->flush();
    }
}
