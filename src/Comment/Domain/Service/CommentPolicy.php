<?php

namespace App\Comment\Domain\Service;

use App\Authentication\Domain\Entity\User;
use App\Comment\Domain\Entity\Comment;

class CommentPolicy
{
    public static function canToggleBlock(Comment $comment, User $user): bool
    {
        if ($user->banned()) {
            return false;
        }

        return $user->admin();
    }

    public static function canDelete(Comment $comment, User $user): bool
    {
        if ($user->banned()) {
            return false;
        }

        if ($comment->getUserId()->value() === $user->getId()->value()) {
            return !$comment->isBlocked();
        }

        return $user->admin();
    }

    public static function canUpdate(Comment $comment, User $user): bool
    {
        if ($user->banned()) {
            return false;
        }

        if ($user->admin()) {
            return true;
        }

        if ($comment->getUserId()->value() === $user->getId()->value()) {
            return !$comment->isBlocked();
        }

        return false;
    }
}
