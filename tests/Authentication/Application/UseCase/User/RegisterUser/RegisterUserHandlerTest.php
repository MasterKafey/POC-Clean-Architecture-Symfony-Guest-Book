<?php

namespace App\Tests\Authentication\Application\UseCase\User\RegisterUser;

use App\Authentication\Application\UseCase\User\RegisterUser\RegisterUserCommand;
use App\Authentication\Application\UseCase\User\RegisterUser\RegisterUserHandler;
use App\Authentication\Domain\Entity\User;
use App\Authentication\Domain\Event\UserRegistered;
use App\Authentication\Domain\Exception\EmailAlreadyUsedException;
use App\Authentication\Domain\Exception\UserAlreadyExistsException;
use App\Authentication\Domain\Port\PasswordHasherPort;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\FirstName;
use App\Authentication\Domain\ValueObject\LastName;
use App\Authentication\Domain\ValueObject\PasswordHash;
use App\Shared\Domain\Port\EventBusPort;
use App\Shared\Domain\Port\UuidGeneratorPort;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\TestCase;

class RegisterUserHandlerTest extends TestCase
{
    public function testRegisterUserSuccessfully()
    {
        $command = new RegisterUserCommand(
            new FirstName('John'),
            new LastName('Doe'),
            new Email('john@example.com'),
            'password'
        );

        $repo = $this->createMock(UserRepositoryInterface::class);
        $uuidPort = $this->createMock(UuidGeneratorPort::class);
        $hasher = $this->createMock(PasswordHasherPort::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $repo->method('findByEmail')->willReturn(null);

        $generatedUuid = TestFactory::uuid();
        $uuidPort->method('generate')->willReturn($generatedUuid);

        $hashedValue = new PasswordHash(password_hash('HASHED_PASSWORD', PASSWORD_BCRYPT));
        $hasher->method('hash')->willReturn($hashedValue);

        $repo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $user) use ($generatedUuid, $hashedValue) {
                return $user->getId()->value() === $generatedUuid
                    && $user->getPassword()->value() === $hashedValue->value();
            }))
            ->willReturnCallback(fn(User $u) => $u);

        $eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(UserRegistered::class));

        $handler = new RegisterUserHandler($repo, $uuidPort, $hasher, $eventBus);

        $result = $handler($command);

        $this->assertInstanceOf(User::class, $result->user());
        $this->assertSame('john@example.com', $result->user()->getEmail()->value());
    }

    public function testThrowsIfUserAlreadyExists()
    {
        $command = new RegisterUserCommand(
            new FirstName('John'),
            new LastName('Doe'),
            new Email('john@example.com'),
            'password'
        );

        $existingUser = TestFactory::makeUser();

        $repo = $this->createMock(UserRepositoryInterface::class);
        $uuidPort = $this->createMock(UuidGeneratorPort::class);
        $hasher = $this->createMock(PasswordHasherPort::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $repo->method('findByEmail')->willReturn($existingUser);

        $handler = new RegisterUserHandler($repo, $uuidPort, $hasher, $eventBus);

        $this->expectException(EmailAlreadyUsedException::class);

        $handler($command);
    }
}
