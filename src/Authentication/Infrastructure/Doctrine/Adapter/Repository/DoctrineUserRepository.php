<?php

namespace App\Authentication\Infrastructure\Doctrine\Adapter\Repository;

use App\Authentication\Domain\Entity\User as UserDomain;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\UserId;
use App\Authentication\Infrastructure\Doctrine\Entity\User as UserDoctrine;
use App\Authentication\Infrastructure\Symfony\Mapper\UserDoctrineMapper;
use App\Shared\Domain\ValueObject\Pagination;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {

    }

    public function save(UserDomain $user): UserDomain
    {
        $entity = $this->entityManager->getRepository(UserDoctrine::class)->find($user->getId()->value());

        if (null === $entity) {
            $entity = new UserDoctrine();
            $this->entityManager->persist($entity);
        }

        UserDoctrineMapper::toEntity($user, $entity);
        $this->entityManager->flush();

        return UserDoctrineMapper::toDomain($entity);
    }

    public function findByEmail(Email $email): ?UserDomain
    {
        $user = $this->entityManager->getRepository(UserDoctrine::class)->findOneBy(['email' => $email->value()]);

        if (null === $user) {
            return null;
        }

        return UserDoctrineMapper::toDomain($user);
    }

    public function findById(UserId $getUserId): ?UserDomain
    {
        $user = $this->entityManager->getRepository(UserDoctrine::class)->find($getUserId->value());

        if (null === $user) {
            return null;
        }

        return UserDoctrineMapper::toDomain($user);
    }

    public function findAll(Pagination $pagination): array
    {
        $users = $this->entityManager->getRepository(UserDoctrine::class)->findBy(
            criteria: [],
            limit: $pagination->getMaxResults(),
            offset: ($pagination->getCurrentPage() - 1) * $pagination->getMaxResults()
        );

        return array_map(function (UserDoctrine $entity) {
            return UserDoctrineMapper::toDomain($entity);
        }, $users);
    }
}
