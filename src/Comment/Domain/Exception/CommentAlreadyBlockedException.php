<?php

namespace App\Comment\Domain\Exception;

use App\Comment\Domain\Entity\Comment;

class CommentAlreadyBlockedException extends DomainException
{
    public function __construct(Comment $comment)
    {
        parent::__construct(sprintf("Comment with id '%s' already blocked.", $comment->getId()));
    }
}
