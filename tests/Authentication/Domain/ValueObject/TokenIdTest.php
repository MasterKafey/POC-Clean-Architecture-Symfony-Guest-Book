<?php

namespace App\Tests\Authentication\Domain\ValueObject;

use App\Authentication\Domain\ValueObject\TokenId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TokenIdTest extends TestCase
{
    public function testValidTokenId(): void
    {
        $tokenId = new TokenId('1f0cb6e7-4f72-67a4-804a-5dab5188bd7d');

        $this->assertSame('1f0cb6e7-4f72-67a4-804a-5dab5188bd7d', $tokenId->value());
    }

    #[DataProvider(methodName: 'invalidTokenIdProvider')]
    public function testInvalidTokenId(string $tokenIdValue): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new TokenId($tokenIdValue);
    }

    public static function invalidTokenIdProvider(): array
    {
        return array_map(fn(string $password) => [$password], [
            '',
            '123e4567e89b12d3a456426614174000',
            'zzzzzzzz-e89b-12d3-a456-426614174000'
        ]);
    }
}
