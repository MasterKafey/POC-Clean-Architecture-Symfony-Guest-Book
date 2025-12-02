<?php

namespace App\Tests\Authentication\Application\UseCase\User\UnbanUser;

use App\Authentication\Application\UseCase\User\UnbanUser\UnbanUserCommand;
use App\Authentication\Application\UseCase\User\UnbanUser\UnbanUserHandler;
use App\Authentication\Domain\Entity\User;
use App\Authentication\Domain\Exception\UserNotBannedException;
use App\Authentication\Domain\Exception\UserNotFoundException;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\ValueObject\UserId;
use App\Authentication\Domain\Exception\UnauthorizedException;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\TestCase;

class UnbanUserHandlerTest extends TestCase
{
    public function testUnbanSuccessfully()
    {
        $target = TestFactory::makeUser(banned: true, admin: false);
        $initiator = TestFactory::makeUser();

        $reflection = new \ReflectionProperty($initiator, 'banned');
        $reflection->setAccessible(true);
        $reflection->setValue($initiator, false);

        $repo = $this->createMock(UserRepositoryInterface::class);

        $repo->method('findById')
            ->willReturnMap([
                [$target->getId(), $target],
                [$initiator->getId(), $initiator],
            ]);

        $repo->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class))
            ->willReturnCallback(fn(User $u) => $u);

        $handler = new UnbanUserHandler($repo);

        $command = new UnbanUserCommand(
            $target->getId(),
            $initiator->getId()
        );

        $handler($command);

        $this->assertFalse($target->banned());
    }

    public function testThrowsIfTargetUserNotFound()
    {
        $initiator = TestFactory::makeUser();

        $repo = $this->createMock(UserRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $handler = new UnbanUserHandler($repo);

        $this->expectException(UserNotFoundException::class);

        $handler(
            new UnbanUserCommand(
                new UserId(TestFactory::uuid()),
                $initiator->getId()
            )
        );
    }

    public function testThrowsIfInitiatorUserNotFound()
    {
        $target = TestFactory::makeUser(true, false);

        $repo = $this->createMock(UserRepositoryInterface::class);

        $repo->method('findById')
            ->willReturnMap([
                [$target->getId(), $target],
                [$this->anything(), null],
            ]);

        $handler = new UnbanUserHandler($repo);

        $this->expectException(UserNotFoundException::class);

        $handler(
            new UnbanUserCommand(
                $target->getId(),
                new UserId(TestFactory::uuid()),
            )
        );
    }

    public function testThrowsIfTargetNotBanned()
    {
        $target = TestFactory::makeUser(admin: false);
        $initiator = TestFactory::makeUser();

        $repo = $this->createMock(UserRepositoryInterface::class);
        $repo->method('findById')
            ->willReturnMap([
                [$target->getId(), $target],
                [$initiator->getId(), $initiator],
            ]);

        $handler = new UnbanUserHandler($repo);

        $this->expectException(UserNotBannedException::class);

        $handler(
            new UnbanUserCommand(
                $target->getId(),
                $initiator->getId()
            )
        );
    }

    public function testThrowsIfNotAuthorized()
    {
        $target = TestFactory::makeUser(banned: true, admin: false);
        $initiator = TestFactory::makeUser(admin: false);

        $repo = $this->createMock(UserRepositoryInterface::class);

        $repo->method('findById')
            ->willReturnMap([
                [$target->getId(), $target],
                [$initiator->getId(), $initiator],
            ]);

        $handler = new UnbanUserHandler($repo);

        $this->expectException(UnauthorizedException::class);

        $handler(
            new UnbanUserCommand(
                $target->getId(),
                $initiator->getId()
            )
        );
    }
}
