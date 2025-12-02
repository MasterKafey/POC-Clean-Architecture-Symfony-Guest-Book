<?php

namespace App\Tests\Comment\Application\UseCase\DeleteComment;

use App\Authentication\Domain\Exception\UserNotFoundException;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Application\UseCase\Comment\DeleteComment\DeleteCommentCommand;
use App\Comment\Application\UseCase\Comment\DeleteComment\DeleteCommentHandler;
use App\Comment\Domain\Event\CommentDeleted;
use App\Comment\Domain\Exception\CommentNotFoundException;
use App\Comment\Domain\Exception\UnauthorizedException;
use App\Comment\Domain\Repository\CommentRepositoryInterface;
use App\Comment\Domain\ValueObject\CommentId;
use App\Shared\Domain\Port\EventBusPort;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\TestCase;

class DeleteCommentHandlerTest extends TestCase
{
    public function testDeleteCommentSuccessfully()
    {
        $user = TestFactory::makeUser(admin: false);
        $comment = TestFactory::makeComment($user->getId());

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $commentRepo->method('findById')->willReturn($comment);
        $userRepo->method('findById')->willReturn($user);

        $commentRepo->expects($this->once())
            ->method('delete')
            ->with($comment->getId());

        $eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(CommentDeleted::class));

        $handler = new DeleteCommentHandler(
            $commentRepo,
            $userRepo,
            $eventBus
        );

        $handler(new DeleteCommentCommand($comment->getId(), $user->getId()));

        $this->assertTrue(true);
    }

    public function testThrowsIfCommentNotFound()
    {
        $user = TestFactory::makeUser();

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $commentRepo->method('findById')->willReturn(null);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $handler = new DeleteCommentHandler($commentRepo, $userRepo, $eventBus);

        $this->expectException(CommentNotFoundException::class);

        $handler(new DeleteCommentCommand(new CommentId(TestFactory::uuid()), $user->getId()));
    }

    public function testThrowsIfUserNotFound()
    {
        $comment = TestFactory::makeComment(new UserId(TestFactory::uuid()));

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $commentRepo->method('findById')->willReturn($comment);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('findById')->willReturn(null);

        $handler = new DeleteCommentHandler($commentRepo, $userRepo, $this->createMock(EventBusPort::class));

        $this->expectException(UserNotFoundException::class);

        $handler(new DeleteCommentCommand($comment->getId(), new UserId(TestFactory::uuid())));
    }

    public function testThrowsIfUnauthorized()
    {
        $user = TestFactory::makeUser(admin: false);
        $otherUser = TestFactory::makeUser(admin: false);
        $comment = TestFactory::makeComment($otherUser->getId());

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $commentRepo->method('findById')->willReturn($comment);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('findById')->willReturn($user);

        $eventBus = $this->createMock(EventBusPort::class);

        $handler = new DeleteCommentHandler($commentRepo, $userRepo, $eventBus);

        $this->expectException(UnauthorizedException::class);

        $handler(new DeleteCommentCommand($comment->getId(), $user->getId()));
    }
}
