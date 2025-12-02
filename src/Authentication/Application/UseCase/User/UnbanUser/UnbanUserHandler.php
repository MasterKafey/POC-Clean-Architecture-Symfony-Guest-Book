<?php

namespace App\Authentication\Application\UseCase\User\UnbanUser;

use App\Authentication\Domain\Exception\UnauthorizedException;
use App\Authentication\Domain\Exception\UserNotBannedException;
use App\Authentication\Domain\Exception\UserNotFoundException;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\Service\UserBanPolicy;

final readonly class UnbanUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    )
    {

    }

    public function __invoke(
        UnbanUserCommand $command,
    ): void
    {
        $target = $this->userRepository->findById($command->targetId());

        if (null === $target) {
            throw new UserNotFoundException($command->targetId());
        }

        $initiator = $this->userRepository->findById($command->initiatorId());

        if (null === $initiator) {
            throw new UserNotFoundException($command->initiatorId());
        }

        if (!$target->banned()) {
            throw new UserNotBannedException($initiator);
        }

        if (!UserBanPolicy::canToggleBan($target, $initiator)) {
            throw new UnauthorizedException(sprintf(
                    "User with id '%s' cannot unban target user with id '%s'",
                    $command->initiatorId(),
                    $command->targetId())
            );
        }

        $target->unban();
        $this->userRepository->save($target);
    }
}
