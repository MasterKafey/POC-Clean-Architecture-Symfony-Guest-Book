<?php

namespace App\Comment\Domain\Exception;

use App\Authentication\Domain\Entity\User;
use App\Comment\Domain\Entity\Comment;

class CommentNotBlockedException extends DomainException
{
    public function __construct(Comment $comment)
    {
        parent::__construct(sprintf("Comment with id '%s' is not blocked", $comment->getId()));
    }
}
