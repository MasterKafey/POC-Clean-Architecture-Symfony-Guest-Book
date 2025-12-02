<?php

namespace App\Authentication\Domain\Event;

use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\FirstName;
use App\Authentication\Domain\ValueObject\LastName;
use App\Authentication\Domain\ValueObject\UserId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\HasOccurredAt;

final readonly class UserRegistered implements DomainEvent
{
    use HasOccurredAt;

    public function __construct(
        private UserId    $userId,
        private Email     $email,
        private FirstName $firstName,
        private LastName  $lastName,
    )
    {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function firstName(): FirstName
    {
        return $this->firstName;
    }

    public function lastName(): LastName
    {
        return $this->lastName;
    }
}
