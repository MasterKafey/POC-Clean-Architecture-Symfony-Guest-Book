<?php

namespace App\Authentication\Application\UseCase\User\AuthenticateUser;

use App\Authentication\Domain\Event\UserAuthenticatedByCredentials;
use App\Authentication\Domain\Exception\InvalidCredentialsException;
use App\Authentication\Domain\Exception\UserBannedException;
use App\Authentication\Domain\Exception\UserNotFoundException;
use App\Authentication\Domain\Port\PasswordHasherPort;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Shared\Domain\Port\EventBusPort;

readonly class AuthenticateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherPort      $passwordHasher,
        private EventBusPort            $eventBus
    )
    {

    }

    public function __invoke(AuthenticateUserQuery $query): AuthenticateUserResult
    {
        $user = $this->userRepository->findByEmail($query->getEmail());

        if (null === $user) {
            throw new UserNotFoundException($query->getEmail());
        }

        if ($user->banned()) {
            throw new UserBannedException($user);
        }

        if (!$this->passwordHasher->verify($user->getPassword(), $query->getPassword())) {
            throw new InvalidCredentialsException();
        }

        $this->eventBus->dispatch(new UserAuthenticatedByCredentials($user->getId(), $user->getEmail()));

        return new AuthenticateUserResult($user);
    }
}
