<?php

namespace App\Comment\Domain\Exception;

use App\Authentication\Domain\Entity\User;
use App\Comment\Domain\Entity\Comment;
use App\Comment\Domain\ValueObject\CommentId;

class CommentNotFoundException extends DomainException
{
    public function __construct(CommentId $id)
    {
        parent::__construct(sprintf("Comment with id '%s' not found.", $id));
    }
}
