<?php

namespace App\Tests\Comment\Domain\ValueObject;

use App\Comment\Domain\ValueObject\CommentId;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CommentIdTest extends TestCase
{
    public function testValidCommentId(): void
    {
        $uuid = TestFactory::uuid();
        $commentId = new CommentId($uuid);

        $this->assertSame($uuid, $commentId->value());
    }

    #[DataProvider(methodName: 'invalidCommentIdProvider')]
    public function testInvalidCommentId(string $commentIdValue): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CommentId($commentIdValue);
    }

    public static function invalidCommentIdProvider(): array
    {
        return array_map(fn(string $password) => [$password], [
            '',
            '123e4567e89b12d3a456426614174000',
            'zzzzzzzz-e89b-12d3-a456-426614174000'
        ]);
    }
}
