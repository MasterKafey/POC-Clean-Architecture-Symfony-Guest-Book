<?php

namespace App\Tests\Authentication\Application\UseCase\User\BanUser;

use App\Authentication\Application\UseCase\User\BanUser\BanUserCommand;
use App\Authentication\Application\UseCase\User\BanUser\BanUserHandler;
use App\Authentication\Domain\Entity\User;
use App\Authentication\Domain\Event\UserBanned;
use App\Authentication\Domain\Exception\UnauthorizedException;
use App\Authentication\Domain\Exception\UserAlreadyBannedException;
use App\Authentication\Domain\Exception\UserNotFoundException;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\ValueObject\UserId;
use App\Shared\Domain\Port\EventBusPort;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\TestCase;

class BanUserHandlerTest extends TestCase
{
    public function testAdminCanBanUserSuccessfully()
    {
        $target = TestFactory::makeUser();
        $initiator = TestFactory::makeUser();

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $userRepo
            ->method('findById')
            ->willReturnMap([
                [$target->getId(), $target],
                [$initiator->getId(), $initiator],
            ]);

        $userRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $savedUser) use ($target) {
                return $savedUser->getId()->value() === $target->getId()->value()
                    && $savedUser->banned() === true;
            }))
            ->willReturnCallback(fn(User $u) => $u);

        $eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(UserBanned::class));

        $handler = new BanUserHandler($userRepo, $eventBus);

        $command = new BanUserCommand(
            $target->getId(),
            $initiator->getId()
        );

        $handler($command);

        $this->assertTrue($target->banned());
    }

    public function testThrowsIfTargetDoesNotExist()
    {
        $userId = TestFactory::uuid();
        $initiator = TestFactory::makeUser();

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $userRepo->method('findById')
            ->willReturnMap([
                [$userId, null],
                [$initiator->getId(), $initiator],
            ]);

        $handler = new BanUserHandler($userRepo, $eventBus);

        $this->expectException(UserNotFoundException::class);

        $handler(new BanUserCommand(new UserId($userId), $initiator->getId()));
    }

    public function testThrowsIfInitiatorDoesNotExist()
    {
        $target = TestFactory::makeUser();
        $uuid = TestFactory::uuid();
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $userRepo->method('findById')
            ->willReturnMap([
                [$target->getId(), $target],
                [$uuid, null],
            ]);

        $handler = new BanUserHandler($userRepo, $eventBus);

        $this->expectException(UserNotFoundException::class);

        $handler(new BanUserCommand($target->getId(), new UserId($uuid)));
    }

    public function testThrowsIfTargetAlreadyBanned()
    {
        $target = TestFactory::makeUser(banned: true);
        $initiator = TestFactory::makeUser();

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $userRepo->method('findById')->willReturnMap([
            [$target->getId(), $target],
            [$initiator->getId(), $initiator],
        ]);

        $handler = new BanUserHandler($userRepo, $eventBus);

        $this->expectException(UserAlreadyBannedException::class);

        $handler(new BanUserCommand($target->getId(), $initiator->getId()));
    }

    public function testThrowsUnauthorizedIfInitiatorIsNotAdmin()
    {
        $target = TestFactory::makeUser();
        $initiator = TestFactory::makeUser(admin: false);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $userRepo->method('findById')->willReturnMap([
            [$target->getId(), $target],
            [$initiator->getId(), $initiator],
        ]);

        $handler = new BanUserHandler($userRepo, $eventBus);

        $this->expectException(UnauthorizedException::class);

        $handler(new BanUserCommand($target->getId(), $initiator->getId()));
    }
}
