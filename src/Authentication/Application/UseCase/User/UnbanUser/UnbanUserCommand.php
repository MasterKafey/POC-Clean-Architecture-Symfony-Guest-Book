<?php

namespace App\Authentication\Application\UseCase\User\UnbanUser;

use App\Authentication\Domain\ValueObject\UserId;

final readonly class UnbanUserCommand
{
    public function __construct(
        private UserId $targetId,
        private UserId $initiatorId,
    )
    {

    }

    public function targetId(): UserId
    {
        return $this->targetId;
    }

    public function initiatorId(): UserId
    {
        return $this->initiatorId;
    }
}
