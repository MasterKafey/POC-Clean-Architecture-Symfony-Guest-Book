<?php

namespace App\Comment\Application\UseCase\Comment\UpdateComment;

use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Domain\ValueObject\CommentId;

final readonly class UpdateCommentCommand
{
    public function __construct(
        private CommentId $id,
        private string    $message,
        private UserId    $initiatorId,
    )
    {

    }

    public function getId(): CommentId
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getUserId(): UserId
    {
        return $this->initiatorId;
    }
}
