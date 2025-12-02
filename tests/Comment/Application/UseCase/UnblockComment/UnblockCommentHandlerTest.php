<?php

namespace App\Tests\Comment\Application\UseCase\UnblockComment;

use App\Authentication\Domain\Entity\User;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\FirstName;
use App\Authentication\Domain\ValueObject\LastName;
use App\Authentication\Domain\ValueObject\PasswordHash;
use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Application\UseCase\Comment\UnblockComment\UnblockCommentCommand;
use App\Comment\Application\UseCase\Comment\UnblockComment\UnblockCommentHandler;
use App\Comment\Domain\Entity\Comment;
use App\Comment\Domain\Event\CommentUnblocked;
use App\Comment\Domain\Exception\CommentNotBlockedException;
use App\Comment\Domain\Exception\CommentNotFoundException;
use App\Comment\Domain\Exception\UnauthorizedException;
use App\Comment\Domain\Repository\CommentRepositoryInterface;
use App\Comment\Domain\ValueObject\CommentId;
use App\Shared\Domain\Port\EventBusPort;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\TestCase;

class UnblockCommentHandlerTest extends TestCase
{
    public function testUnblockCommentSuccessfully(): void
    {
        $initiator = TestFactory::makeUser();
        $comment = TestFactory::makeComment($initiator->getId(), blocked: true);

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $commentRepo->method('findById')->willReturn($comment);
        $userRepo->method('findById')->willReturn($initiator);

        $commentRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Comment $c) {
                return $c->isBlocked() === false;
            }))
            ->willReturnCallback(fn(Comment $c) => $c);

        $eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(CommentUnblocked::class));

        $handler = new UnblockCommentHandler($commentRepo, $userRepo, $eventBus);

        $command = new UnblockCommentCommand(
            $comment->getId(),
            $initiator->getId()
        );

        $handler($command);

        $this->assertFalse($comment->isBlocked());
    }

    public function testThrowsWhenCommentNotFound(): void
    {
        $initiator = TestFactory::makeUser();

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $commentRepo->method('findById')->willReturn(null);

        $handler = new UnblockCommentHandler($commentRepo, $userRepo, $eventBus);

        $this->expectException(CommentNotFoundException::class);

        $handler(new UnblockCommentCommand(
            new CommentId(TestFactory::uuid()),
            $initiator->getId()
        ));
    }

    public function testThrowsWhenCommentNotBlocked(): void
    {
        $initiator = TestFactory::makeUser();
        $comment = TestFactory::makeComment($initiator->getId());

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $commentRepo->method('findById')->willReturn($comment);
        $userRepo->method('findById')->willReturn($initiator);

        $handler = new UnblockCommentHandler($commentRepo, $userRepo, $eventBus);

        $this->expectException(CommentNotBlockedException::class);

        $handler(new UnblockCommentCommand(
            $comment->getId(),
            $initiator->getId()
        ));
    }

    public function testThrowsWhenUnauthorized(): void
    {
        $initiator = TestFactory::makeUser(admin: false);
        $comment = TestFactory::makeComment($initiator->getId(), blocked: true);

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $commentRepo->method('findById')->willReturn($comment);
        $userRepo->method('findById')->willReturn($initiator);

        $handler = new UnblockCommentHandler($commentRepo, $userRepo, $eventBus);

        $this->expectException(UnauthorizedException::class);

        $handler(new UnblockCommentCommand(
            $comment->getId(),
            $initiator->getId()
        ));
    }
}
