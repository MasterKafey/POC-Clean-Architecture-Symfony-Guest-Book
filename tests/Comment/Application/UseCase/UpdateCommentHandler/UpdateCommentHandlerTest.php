<?php

namespace App\Tests\Comment\Application\UseCase\UpdateCommentHandler;

use App\Authentication\Domain\Entity\User;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\FirstName;
use App\Authentication\Domain\ValueObject\LastName;
use App\Authentication\Domain\ValueObject\PasswordHash;
use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Application\UseCase\Comment\UpdateComment\UpdateCommentCommand;
use App\Comment\Application\UseCase\Comment\UpdateComment\UpdateCommentHandler;
use App\Comment\Application\UseCase\Comment\UpdateComment\UpdateCommentResult;
use App\Comment\Domain\Entity\Comment;
use App\Comment\Domain\Event\CommentUpdated;
use App\Comment\Domain\Exception\CommentNotFoundException;
use App\Comment\Domain\Exception\UnauthorizedException;
use App\Comment\Domain\Repository\CommentRepositoryInterface;
use App\Comment\Domain\ValueObject\CommentId;
use App\Shared\Domain\Port\EventBusPort;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\TestCase;

class UpdateCommentHandlerTest extends TestCase
{
    public function testUpdateCommentSuccessfully(): void
    {
        $oldMessage = 'Hello World! This is my old message.';
        $newMessage = 'Hello World! This is my new message.';
        $user = TestFactory::makeUser();
        $comment = TestFactory::makeComment($user->getId(), message: $oldMessage);

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $commentRepo->method('findById')->willReturn($comment);
        $userRepo->method('findById')->willReturn($user);

        $commentRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(Comment $c) => $c->getMessage() === $newMessage))
            ->willReturnCallback(fn(Comment $c) => $c);

        $eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (CommentUpdated $event) use ($comment, $oldMessage, $newMessage) {
                return $event->oldMessage() === $oldMessage
                    && $event->newMessage() === $newMessage
                    && $event->id()->value() === $comment->getId()->value();
            }));

        $handler = new UpdateCommentHandler($commentRepo, $userRepo, $eventBus);

        $command = new UpdateCommentCommand(
            $comment->getId(),
            $newMessage,
            $user->getId(),
        );

        $result = $handler($command);

        $this->assertSame($newMessage, $result->comment()->getMessage());
    }

    public function testThrowsWhenCommentNotFound(): void
    {
        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $commentRepo->method('findById')->willReturn(null);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $handler = new UpdateCommentHandler($commentRepo, $userRepo, $eventBus);

        $this->expectException(CommentNotFoundException::class);

        $handler(new UpdateCommentCommand(
            new CommentId(TestFactory::uuid()),
            'New content',
            new UserId(TestFactory::uuid()),
        ));
    }

    public function testThrowsWhenNotAllowed(): void
    {
        $message = 'Hello World!';
        $author = TestFactory::makeUser(admin: false);
        $otherUser = TestFactory::makeUser(admin: false);

        $comment = TestFactory::makeComment($author->getId(), $message);

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $commentRepo->method('findById')->willReturn($comment);
        $userRepo->method('findById')->willReturn($otherUser);

        $handler = new UpdateCommentHandler($commentRepo, $userRepo, $eventBus);

        $this->expectException(UnauthorizedException::class);

        $handler(new UpdateCommentCommand(
            $comment->getId(),
            $message,
            $otherUser->getId(),
        ));
    }
}
