<?php

namespace App\Comment\Domain\Exception;

use App\Authentication\Domain\ValueObject\UserId;

class UserCommentAlreadyExists extends DomainException
{
    public function __construct(UserId $userId)
    {
        parent::__construct(sprintf("User with id '%s' already create a comment", $userId));
    }
}
