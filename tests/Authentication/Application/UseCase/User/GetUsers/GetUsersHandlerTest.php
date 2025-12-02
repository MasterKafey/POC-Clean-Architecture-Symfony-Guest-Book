<?php

namespace App\Tests\Authentication\Application\UseCase\User\GetUsers;

use App\Authentication\Application\UseCase\User\GetUsers\GetUsersHandler;
use App\Authentication\Application\UseCase\User\GetUsers\GetUsersQuery;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Shared\Domain\ValueObject\Pagination;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\TestCase;

class GetUsersHandlerTest extends TestCase
{
    public function testGetUsersReturnsResultObject()
    {
        $query = new GetUsersQuery(new Pagination(1, 10));

        $repo = $this->createMock(UserRepositoryInterface::class);

        $users = [
            TestFactory::makeUser(),
            TestFactory::makeUser(),
        ];

        $repo->expects($this->once())
            ->method('findAll')
            ->with($query->getPagination())
            ->willReturn($users);

        $handler = new GetUsersHandler($repo);
        $result = $handler($query);

        $this->assertSame($users, $result->getUsers());
    }

    public function testGetUsersReturnsEmptyArray()
    {
        $query = new GetUsersQuery(new Pagination(1, 10));

        $repo = $this->createMock(UserRepositoryInterface::class);

        $repo->expects($this->once())
            ->method('findAll')
            ->with($query->getPagination())
            ->willReturn([]);

        $handler = new GetUsersHandler($repo);
        $result = $handler($query);

        $this->assertSame([], $result->getUsers());
    }
}
