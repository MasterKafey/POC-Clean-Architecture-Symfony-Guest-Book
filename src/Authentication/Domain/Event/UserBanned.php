<?php

namespace App\Authentication\Domain\Event;

use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\UserId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\HasOccurredAt;

final readonly class UserBanned implements DomainEvent
{
    use HasOccurredAt;

    public function __construct(
        private UserId $bannedUserId,
        private Email  $bannedUserEmail,
        private UserId $initiatorUserId,
        private Email  $initiatorUserEmail,
    )
    {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function bannedUserId(): UserId
    {
        return $this->bannedUserId;
    }

    public function bannedUserEmail(): Email
    {
        return $this->bannedUserEmail;
    }

    public function initiatorUserId(): UserId
    {
        return $this->initiatorUserId;
    }

    public function initiatorUserEmail(): Email
    {
        return $this->initiatorUserEmail;
    }
}
