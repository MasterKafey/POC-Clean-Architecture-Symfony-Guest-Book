<?php

namespace App\Tests\Authentication\Application\UseCase\User\AuthenticateUser;

use App\Authentication\Application\UseCase\User\AuthenticateUser\AuthenticateUserHandler;
use App\Authentication\Application\UseCase\User\AuthenticateUser\AuthenticateUserQuery;
use App\Authentication\Application\UseCase\User\AuthenticateUser\AuthenticateUserResult;
use App\Authentication\Domain\Event\UserAuthenticatedByCredentials;
use App\Authentication\Domain\Exception\InvalidCredentialsException;
use App\Authentication\Domain\Exception\UserBannedException;
use App\Authentication\Domain\Exception\UserNotFoundException;
use App\Authentication\Domain\Port\PasswordHasherPort;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\PasswordHash;
use App\Shared\Domain\Port\EventBusPort;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\TestCase;

final class AuthenticateUserHandlerTest extends TestCase
{
    public function testAuthenticateSuccessfully()
    {
        $user = TestFactory::makeUser();

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $hasher   = $this->createMock(PasswordHasherPort::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $userRepo
            ->method('findByEmail')
            ->willReturn($user);

        $hasher
            ->method('verify')
            ->with(
                $this->isInstanceOf(PasswordHash::class),
                'password'
            )
            ->willReturn(true);

        $eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(UserAuthenticatedByCredentials::class));

        $handler = new AuthenticateUserHandler(
            $userRepo,
            $hasher,
            $eventBus
        );

        $query = new AuthenticateUserQuery(
            new Email('john@example.com'),
            'password'
        );

        $result = $handler($query);

        $this->assertInstanceOf(AuthenticateUserResult::class, $result);
        $this->assertSame($user, $result->getUser());
    }

    public function testThrowsIfUserNotFound()
    {
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $hasher   = $this->createMock(PasswordHasherPort::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $userRepo->method('findByEmail')->willReturn(null);

        $handler = new AuthenticateUserHandler(
            $userRepo,
            $hasher,
            $eventBus
        );

        $this->expectException(UserNotFoundException::class);

        $handler(new AuthenticateUserQuery(new Email('nope@x.com'), 'password'));
    }

    public function testThrowsIfUserIsBanned()
    {
        $user = TestFactory::makeUser(banned: true);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $hasher   = $this->createMock(PasswordHasherPort::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $userRepo->method('findByEmail')->willReturn($user);

        $handler = new AuthenticateUserHandler(
            $userRepo,
            $hasher,
            $eventBus
        );

        $this->expectException(UserBannedException::class);

        $handler(new AuthenticateUserQuery(new Email('john@example.com'), 'password'));
    }

    public function testThrowsIfPasswordInvalid()
    {
        $user = TestFactory::makeUser();

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $hasher   = $this->createMock(PasswordHasherPort::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $userRepo->method('findByEmail')->willReturn($user);

        $hasher->method('verify')->willReturn(false);

        $handler = new AuthenticateUserHandler(
            $userRepo,
            $hasher,
            $eventBus
        );

        $this->expectException(InvalidCredentialsException::class);

        $handler(new AuthenticateUserQuery(new Email('john@example.com'), 'wrong-password'));
    }
}
