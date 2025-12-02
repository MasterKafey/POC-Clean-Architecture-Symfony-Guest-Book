<?php

namespace App\Tests\Comment\Application\UseCase\CreateComment;

use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Application\UseCase\Comment\CreateComment\CreateCommentCommand;
use App\Comment\Application\UseCase\Comment\CreateComment\CreateCommentHandler;
use App\Comment\Domain\Entity\Comment;
use App\Comment\Domain\Event\CommentCreated;
use App\Comment\Domain\Exception\UserCommentAlreadyExists;
use App\Comment\Domain\Repository\CommentRepositoryInterface;
use App\Shared\Domain\Port\EventBusPort;
use App\Shared\Domain\Port\UuidGeneratorPort;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\TestCase;

class CreateCommentHandlerTest extends TestCase
{
    public function testCreateCommentSuccessfully()
    {
        $userId = new UserId(TestFactory::uuid());
        $message = 'Hello world';
        $command = new CreateCommentCommand($userId, $message);

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $uuidPort = $this->createMock(UuidGeneratorPort::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $commentRepo->method('findByUserId')->willReturn(null);

        $newUuid = TestFactory::uuid();
        $uuidPort->method('generate')->willReturn($newUuid);

        $commentRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(Comment $c) =>
                $c->getUserId()->value() === $userId->value()
                && $c->getMessage() === $message
            ))
            ->willReturnCallback(fn(Comment $c) => $c);

        $eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(CommentCreated::class));

        $handler = new CreateCommentHandler($commentRepo, $uuidPort, $eventBus);

        $handler($command);

        $this->assertTrue(true);
    }

    public function testThrowsIfCommentAlreadyExistsForUser()
    {
        $userId = new UserId(TestFactory::uuid());
        $existingComment = TestFactory::makeComment($userId);

        $commentRepo = $this->createMock(CommentRepositoryInterface::class);
        $uuidPort = $this->createMock(UuidGeneratorPort::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $commentRepo->method('findByUserId')->willReturn($existingComment);

        $handler = new CreateCommentHandler($commentRepo, $uuidPort, $eventBus);

        $this->expectException(UserCommentAlreadyExists::class);

        $handler(new CreateCommentCommand($userId, 'Hello'));
    }
}
