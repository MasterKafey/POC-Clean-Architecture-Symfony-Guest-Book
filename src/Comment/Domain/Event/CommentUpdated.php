<?php

namespace App\Comment\Domain\Event;

use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Domain\ValueObject\CommentId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\HasOccurredAt;

final readonly class CommentUpdated implements DomainEvent
{
    use HasOccurredAt;

    public function __construct(
        private CommentId $id,
        private UserId    $userId,
        private Email     $userEmail,
        private string    $oldMessage,
        private string    $newMessage,
    )
    {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function id(): CommentId
    {
        return $this->id;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function userEmail(): Email
    {
        return $this->userEmail;
    }

    public function oldMessage(): string
    {
        return $this->oldMessage;
    }

    public function newMessage(): string
    {
        return $this->newMessage;
    }
}
