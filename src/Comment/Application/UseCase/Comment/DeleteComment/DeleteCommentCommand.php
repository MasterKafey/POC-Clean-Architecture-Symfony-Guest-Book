<?php

namespace App\Comment\Application\UseCase\Comment\DeleteComment;

use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Domain\ValueObject\CommentId;

final readonly class DeleteCommentCommand
{
    public function __construct(
        private CommentId $commentId,
        private UserId $userId,
    )
    {

    }

    public function commentId(): CommentId
    {
        return $this->commentId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }
}
