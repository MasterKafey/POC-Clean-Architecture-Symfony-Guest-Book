<?php

namespace App\Authentication\Domain\Event;

use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\UserId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\HasOccurredAt;

final readonly class UserUnbanned implements DomainEvent
{
    use HasOccurredAt;

    public function __construct(
        private UserId $unbannedUserId,
        private Email  $unbannedUserEmail,
        private UserId $initiatorUserId,
        private Email  $initiatorUserEmail,
    )
    {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function unbannedUserId(): UserId
    {
        return $this->unbannedUserId;
    }

    public function unbannedUserEmail(): Email
    {
        return $this->unbannedUserEmail;
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
