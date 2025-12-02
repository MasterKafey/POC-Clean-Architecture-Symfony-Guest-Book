<?php

namespace App\Comment\Domain\Entity;

use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Domain\ValueObject\CommentId;

final class Comment
{
    public function __construct(
        private readonly CommentId          $id,
        private readonly UserId             $userId,
        private string                      $message,
        private readonly \DateTimeImmutable $createdAt,
        private bool                        $blocked = false,
    )
    {
    }

    public function getId(): CommentId
    {
        return $this->id;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isBlocked(): bool
    {
        return $this->blocked;
    }

    public function setBlocked(bool $blocked): self
    {
        $this->blocked = $blocked;
        return $this;
    }
}
