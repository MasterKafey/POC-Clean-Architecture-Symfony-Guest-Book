<?php

namespace App\Authentication\Application\UseCase\User\RegisterUser;

use App\Authentication\Domain\Entity\User;
use App\Authentication\Domain\Event\UserRegistered;
use App\Authentication\Domain\Exception\EmailAlreadyUsedException;
use App\Authentication\Domain\Exception\UserAlreadyExistsException;
use App\Authentication\Domain\Port\PasswordHasherPort;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\ValueObject\UserId;
use App\Shared\Domain\Port\EventBusPort;
use App\Shared\Domain\Port\UuidGeneratorPort;

final readonly class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UuidGeneratorPort       $uuidGenerator,
        private PasswordHasherPort      $passwordHasher,
        private EventBusPort            $eventBus,
    )
    {
    }

    public function __invoke(
        RegisterUserCommand $command
    ): RegisterUserResult
    {
        $user = $this->userRepository->findByEmail($command->email());
        if (null !== $user) {
            throw new EmailAlreadyUsedException($command->email());
        }

        $userId = new UserId($this->uuidGenerator->generate());
        $hash = $this->passwordHasher->hash($command->plainPassword());

        $user = new User(
            $userId,
            $command->firstName(),
            $command->lastName(),
            $command->email(),
            $hash,
        );

        $user = $this->userRepository->save($user);

        $this->eventBus->dispatch(new UserRegistered(
            $user->getId(),
            $user->getEmail(),
            $user->getFirstName(),
            $user->getLastName(),
        ));

        return new RegisterUserResult($user);
    }
}
