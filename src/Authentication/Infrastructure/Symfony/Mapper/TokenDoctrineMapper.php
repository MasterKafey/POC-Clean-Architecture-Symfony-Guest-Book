<?php

namespace App\Authentication\Infrastructure\Symfony\Mapper;

use App\Authentication\Domain\Entity\Token as TokenDomain;
use App\Authentication\Domain\ValueObject\TokenId;
use App\Authentication\Domain\ValueObject\TokenValue;
use App\Authentication\Domain\ValueObject\UserId;
use App\Authentication\Infrastructure\Doctrine\Entity\Token as TokenDoctrine;

final readonly class TokenDoctrineMapper
{
    public static function toEntity(TokenDomain $domain, TokenDoctrine $entity): TokenDoctrine
    {
        $entity
            ->setId($domain->getId())
            ->setExpiresAt($domain->getExpiresAt())
            ->setTokenType($domain->getTokenType())
            ->setUserId($domain->getUserId())
            ->setValue($domain->getValue())
        ;

        return $entity;
    }

    public static function toDomain(TokenDoctrine $entity): TokenDomain
    {
        return new TokenDomain(
            new TokenId($entity->getId()),
            new UserId($entity->getUserId()),
            $entity->getTokenType(),
            new TokenValue($entity->getValue()),
            $entity->getExpiresAt(),
        );
    }
}
