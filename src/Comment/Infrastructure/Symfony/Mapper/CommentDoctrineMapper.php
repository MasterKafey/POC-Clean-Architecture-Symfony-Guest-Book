<?php

namespace App\Comment\Infrastructure\Symfony\Mapper;

use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Domain\Entity\Comment as CommentDomain;
use App\Comment\Domain\ValueObject\CommentId;
use App\Comment\Infrastructure\Doctrine\Entity\Comment as CommentDoctrine;
use App\Authentication\Infrastructure\Doctrine\Entity\User as UserDoctrine;

final readonly class CommentDoctrineMapper
{
    public static function toEntity(
        CommentDomain   $commentDomain,
        CommentDoctrine $commentDoctrine,
        UserDoctrine    $userDoctrine
    ): CommentDoctrine
    {
        $commentDoctrine
            ->setUser($userDoctrine)
            ->setBlocked($commentDomain->isBlocked())
            ->setCreatedAt($commentDomain->getCreatedAt())
            ->setMessage($commentDomain->getMessage());

        return $commentDoctrine;
    }

    public static function toDomain(CommentDoctrine $commentDoctrine): CommentDomain
    {
        return new CommentDomain(
            new CommentId($commentDoctrine->getId()),
            new UserId($commentDoctrine->getUser()->getId()),
            $commentDoctrine->getMessage(),
            $commentDoctrine->getCreatedAt(),
            $commentDoctrine->getBlocked(),
        );
    }
}
