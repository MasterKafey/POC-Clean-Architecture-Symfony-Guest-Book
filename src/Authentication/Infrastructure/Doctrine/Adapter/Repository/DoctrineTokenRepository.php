<?php

namespace App\Authentication\Infrastructure\Doctrine\Adapter\Repository;

use App\Authentication\Domain\Entity\Token as TokenDomain;
use App\Authentication\Domain\Repository\TokenRepositoryInterface;
use App\Authentication\Domain\ValueObject\TokenValue;
use App\Authentication\Infrastructure\Doctrine\Entity\Token as TokenDoctrine;
use App\Authentication\Infrastructure\Symfony\Mapper\TokenDoctrineMapper;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineTokenRepository implements TokenRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {

    }

    public function save(TokenDomain $token): TokenDomain
    {
        $entity = $this->entityManager->getRepository(TokenDoctrine::class)->find($token->getId()->value());

        if (null === $entity) {
            $entity = new TokenDoctrine();
            $this->entityManager->persist($entity);
        }

        TokenDoctrineMapper::toEntity($token, $entity);
        $this->entityManager->flush();

        return TokenDoctrineMapper::toDomain($entity);
    }

    public function findTokenByValue(TokenValue $value): ?TokenDomain
    {
        $token = $this->entityManager->getRepository(TokenDoctrine::class)->findOneBy(['value' => $value]);

        if (null === $token) {
            return null;
        }

        return TokenDoctrineMapper::toDomain($token);
    }

    public function remove(TokenDomain $token): void
    {
        $token = $this->entityManager->getRepository(TokenDoctrine::class)->find($token->getId()->value());

        $this->entityManager->remove($token);
        $this->entityManager->flush();
    }
}
