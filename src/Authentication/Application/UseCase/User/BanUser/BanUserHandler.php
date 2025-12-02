<?php

namespace App\Authentication\Application\UseCase\User\BanUser;

use App\Authentication\Domain\Event\UserBanned;
use App\Authentication\Domain\Exception\UnauthorizedException;
use App\Authentication\Domain\Exception\UserAlreadyBannedException;
use App\Authentication\Domain\Exception\UserNotFoundException;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\Service\UserBanPolicy;
use App\Shared\Domain\Port\EventBusPort;

final readonly class BanUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EventBusPort            $eventBus,
    )
    {
    }

    public function __invoke(BanUserCommand $command): void
    {
        $target = $this->userRepository->findById($command->targetId());

        if (null === $target) {
            throw new UserNotFoundException($command->targetId());
        }

        $initiator = $this->userRepository->findById($command->initiatorId());

        if (null === $initiator) {
            throw new UserNotFoundException($command->initiatorId());
        }


        if ($target->banned()) {
            throw new UserAlreadyBannedException($target);
        }

        if (!UserBanPolicy::canToggleBan($target, $initiator)) {
            throw new UnauthorizedException(sprintf(
                    "User with id '%s' cannot unban target user with id '%s'",
                    $command->initiatorId(),
                    $command->targetId())
            );
        }

        $target->ban();
        $this->userRepository->save($target);

        $this->eventBus->dispatch(new UserBanned($target->getId(), $target->getEmail(), $initiator->getId(), $initiator->getEmail()));
    }
}
