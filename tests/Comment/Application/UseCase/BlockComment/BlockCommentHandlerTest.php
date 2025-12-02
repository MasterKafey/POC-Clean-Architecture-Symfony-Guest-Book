<?php

namespace App\Tests\Comment\Application\UseCase\BlockComment;

use App\Authentication\Domain\Entity\User;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\FirstName;
use App\Authentication\Domain\ValueObject\LastName;
use App\Authentication\Domain\ValueObject\PasswordHash;
use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Application\UseCase\Comment\BlockComment\BlockCommentCommand;
use App\Comment\Application\UseCase\Comment\BlockComment\BlockCommentHandler;
use App\Comment\Domain\Entity\Comment;
use App\Comment\Domain\Event\CommentBlocked;
use App\Comment\Domain\Exception\CommentAlreadyBlockedException;
use App\Comment\Domain\Exception\CommentNotFoundException;
use App\Comment\Domain\Exception\UnauthorizedException;
use App\Comment\Domain\Repository\CommentRepositoryInterface;
use App\Comment\Domain\ValueObject\CommentId;
use App\Shared\Domain\Port\EventBusPort;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\TestCase;

class BlockCommentHandlerTest extends TestCase
{
    public function testBlockCommentSuccessfully()
    {
        $initiator = TestFactory::makeUser();
        $comment = TestFactory::makeComment($initiator->getId());

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $commentRepo->method('findById')->willReturn($comment);
        $userRepo->method('findById')->willReturn($initiator);

        $commentRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($c) {
                return $c instanceof Comment;
            }))
            ->willReturnCallback(fn($comment) => $comment);

        $eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(CommentBlocked::class));

        $handler = new BlockCommentHandler($commentRepo, $userRepo, $eventBus);

        $handler(
            new BlockCommentCommand(
                $comment->getId(),
                $initiator->getId()
            )
        );

        $this->assertTrue($comment->isBlocked());
    }

    public function testThrowsWhenCommentNotFound()
    {
        $initiator = TestFactory::makeUser();

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $commentRepo->method('findById')->willReturn(null);

        $handler = new BlockCommentHandler($commentRepo, $userRepo, $eventBus);

        $this->expectException(CommentNotFoundException::class);

        $handler(
            new BlockCommentCommand(
                new CommentId(TestFactory::uuid()),
                $initiator->getId()
            )
        );
    }

    public function testThrowsWhenCommentAlreadyBlocked()
    {
        $initiator = TestFactory::makeUser();
        $comment = TestFactory::makeComment($initiator->getId(), blocked: true);

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $commentRepo->method('findById')->willReturn($comment);
        $userRepo->method('findById')->willReturn($initiator);

        $handler = new BlockCommentHandler($commentRepo, $userRepo, $eventBus);

        $this->expectException(CommentAlreadyBlockedException::class);

        $handler(
            new BlockCommentCommand(
                $comment->getId(),
                $initiator->getId()
            )
        );
    }

    public function testThrowsIfNotAuthorized()
    {
        $initiator = TestFactory::makeUser(admin: false);
        $comment = TestFactory::makeComment($initiator->getId());

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $commentRepo->method('findById')->willReturn($comment);
        $userRepo->method('findById')->willReturn($initiator);

        $handler = new BlockCommentHandler($commentRepo, $userRepo, $eventBus);

        $this->expectException(UnauthorizedException::class);

        $handler(
            new BlockCommentCommand(
                $comment->getId(),
                $initiator->getId()
            )
        );
    }
}
