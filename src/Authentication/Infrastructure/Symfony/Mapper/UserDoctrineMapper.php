<?php

namespace App\Authentication\Infrastructure\Symfony\Mapper;

use App\Authentication\Domain\Entity\User as UserDomain;
use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\FirstName;
use App\Authentication\Domain\ValueObject\LastName;
use App\Authentication\Domain\ValueObject\PasswordHash;
use App\Authentication\Domain\ValueObject\UserId;
use App\Authentication\Infrastructure\Doctrine\Entity\User as UserDoctrine;

final class UserDoctrineMapper
{
    public static function toDomain(UserDoctrine $entity): UserDomain
    {
        return new UserDomain(
            new UserId($entity->getId()),
            new FirstName($entity->getFirstName()),
            new LastName($entity->getLastName()),
            new Email($entity->getEmail()),
            new PasswordHash($entity->getPassword()),
            in_array('ROLE_ADMIN', $entity->getRoles()),
            $entity->getBanned(),
            $entity->isValidated()
        );
    }

    public static function toEntity(UserDomain $userDomain, UserDoctrine $userDoctrine): UserDoctrine
    {
        $userDoctrine
            ->setFirstName($userDomain->getFirstName())
            ->setLastName($userDomain->getLastName())
            ->setEmail($userDomain->getEmail())
            ->setPassword($userDomain->getPassword())
            ->setRoles($userDomain->admin() ? ['ROLE_ADMIN'] : ['ROLE_USER'])
            ->setBanned($userDomain->banned())
            ->setValidated($userDomain->validated());;

        return $userDoctrine;
    }
}
