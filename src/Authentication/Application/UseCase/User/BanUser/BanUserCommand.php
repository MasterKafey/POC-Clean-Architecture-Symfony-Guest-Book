<?php

namespace App\Authentication\Application\UseCase\User\BanUser;

use App\Authentication\Domain\ValueObject\UserId;

final readonly class BanUserCommand
{
    public function __construct(
        private UserId $targetUserId,
        private UserId $initiatorUserid
    )
    {

    }

    public function targetId(): UserId
    {
        return $this->targetUserId;
    }

    public function initiatorId(): UserId
    {
        return $this->initiatorUserid;
    }
}
