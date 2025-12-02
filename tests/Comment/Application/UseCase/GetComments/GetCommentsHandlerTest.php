<?php

namespace App\Tests\Comment\Application\UseCase\GetComments;

use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Application\UseCase\Comment\GetComments\GetCommentsHandler;
use App\Comment\Application\UseCase\Comment\GetComments\GetCommentsQuery;
use App\Comment\Application\UseCase\Comment\GetComments\GetCommentsResult;
use App\Comment\Domain\Entity\Comment;
use App\Comment\Domain\Repository\CommentRepositoryInterface;
use App\Comment\Domain\ValueObject\CommentId;
use App\Shared\Domain\ValueObject\Pagination;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\TestCase;

class GetCommentsHandlerTest extends TestCase
{
    public function testGetCommentsIncludingBlocked()
    {
        $pagination = new Pagination(1, 10);

        $comments = [
            TestFactory::makeComment(new UserId(TestFactory::uuid())),
            TestFactory::makeComment(new UserId(TestFactory::uuid())),
        ];

        $observerRepo = $this->createMock(CommentRepositoryInterface::class);

        $observerRepo->expects($this->once())
            ->method('findAll')
            ->with($pagination)
            ->willReturn($comments);

        $observerRepo->expects($this->never())->method('findNotBlocked');

        $handler = new GetCommentsHandler($observerRepo);

        $query = new GetCommentsQuery($pagination, includeBlocked: true);

        $result = $handler($query);

        $this->assertSame($comments, $result->comments());
    }

    public function testGetCommentsOnlyNotBlocked()
    {
        $pagination = new Pagination(2, 5);

        $comments = [
            TestFactory::makeComment(new UserId(TestFactory::uuid())),
        ];

        $observerRepo = $this->createMock(CommentRepositoryInterface::class);

        $observerRepo->expects($this->once())
            ->method('findNotBlocked')
            ->with($pagination)
            ->willReturn($comments);

        $observerRepo->expects($this->never())->method('findAll');

        $handler = new GetCommentsHandler($observerRepo);

        $query = new GetCommentsQuery($pagination, includeBlocked: false);

        $result = $handler($query);

        $this->assertCount(1, $result->comments());
        $this->assertSame($comments, $result->comments());
    }
}
