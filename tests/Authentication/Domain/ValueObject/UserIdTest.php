<?php

namespace App\Tests\Authentication\Domain\ValueObject;

use App\Authentication\Domain\ValueObject\UserId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    public function testValidUserId(): void
    {
        $userId = new UserId('1f0cb6e7-4f72-67a4-804a-5dab5188bd7d');

        $this->assertSame('1f0cb6e7-4f72-67a4-804a-5dab5188bd7d', $userId->value());
    }

    #[DataProvider(methodName: 'invalidUserIdProvider')]
    public function testInvalidUserId(string $userIdValue): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new UserId($userIdValue);
    }

    public static function invalidUserIdProvider(): array
    {
        return array_map(fn(string $password) => [$password], [
            '',
            '123e4567e89b12d3a456426614174000',
            'zzzzzzzz-e89b-12d3-a456-426614174000'
        ]);
    }
}
