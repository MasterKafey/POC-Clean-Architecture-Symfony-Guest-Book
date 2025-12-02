<?php

namespace App\Authentication\Domain\Service;

use App\Authentication\Domain\Entity\User;

class UserBanPolicy
{
    public static function canToggleBan(User $target, User $initiator): bool
    {
        if ($initiator->getId()->value() === $target->getId()->value()) {
            return false;
        }

        return $initiator->admin() && !$initiator->banned();
    }
}
