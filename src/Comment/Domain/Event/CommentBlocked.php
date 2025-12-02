<?php

namespace App\Comment\Domain\Event;

use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Domain\ValueObject\CommentId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\HasOccurredAt;

final readonly class CommentBlocked implements DomainEvent
{
    use HasOccurredAt;

    public function __construct(
        private CommentId $commentId,
        private string    $message,
        private UserId    $initiatorUserId,
        private Email     $initiatorEmail,
    )
    {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function commentId(): CommentId
    {
        return $this->commentId;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function initiatorUserId(): UserId
    {
        return $this->initiatorUserId;
    }

    public function initiatorEmail(): Email
    {
        return $this->initiatorEmail;
    }
}
