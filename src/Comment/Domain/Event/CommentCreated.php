<?php

namespace App\Comment\Domain\Event;

use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Domain\ValueObject\CommentId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\HasOccurredAt;

final readonly class CommentCreated implements DomainEvent
{
    use HasOccurredAt;

    public function __construct(
        private CommentId $commentId,
        private UserId    $userId,
        private string    $message,
    )
    {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function commentId(): CommentId
    {
        return $this->commentId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function message(): string
    {
        return $this->message;
    }
}
