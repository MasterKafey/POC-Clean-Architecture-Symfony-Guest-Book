<?php

namespace App\Comment\Application\UseCase\Comment\CreateComment;

use App\Authentication\Domain\ValueObject\UserId;

final readonly class CreateCommentCommand
{
    public function __construct(
        private UserId $userId,
        private string $message,
    )
    {

    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
