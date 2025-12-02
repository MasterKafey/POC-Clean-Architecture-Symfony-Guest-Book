<?php

namespace App\Comment\Application\UseCase\Comment\UnblockComment;

use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Domain\ValueObject\CommentId;

final readonly class UnblockCommentCommand
{
    public function __construct(
        private CommentId $commentId,
        private UserId    $initiatorId,
    )
    {

    }

    public function getCommentId(): CommentId
    {
        return $this->commentId;
    }

    public function getInitiatorId(): UserId
    {
        return $this->initiatorId;
    }
}
