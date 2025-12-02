<?php

namespace App\Comment\Application\UseCase\Comment\UpdateComment;

use App\Comment\Domain\Entity\Comment;

final readonly class UpdateCommentResult
{
    public function __construct(
        private Comment $comment
    )
    {
    }

    public function comment(): Comment
    {
        return $this->comment;
    }
}
