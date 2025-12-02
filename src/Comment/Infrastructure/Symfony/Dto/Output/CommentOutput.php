<?php

namespace App\Comment\Infrastructure\Symfony\Dto\Output;

use App\Comment\Domain\Entity\Comment;

final readonly class CommentOutput
{
    public function __construct(
        private string             $id,
        private string             $userId,
        private string             $message,
        private \DateTimeImmutable $createdAt,
    )
    {
    }

    public static function fromDomain(Comment $comment): self
    {
        return new self(
            $comment->getId(),
            $comment->getUserId(),
            $comment->getMessage(),
            $comment->getCreatedAt()
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): string
    {
        return $this->userId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
