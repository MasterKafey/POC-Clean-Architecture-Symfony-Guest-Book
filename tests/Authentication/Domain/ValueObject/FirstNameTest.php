<?php

namespace App\Tests\Authentication\Domain\ValueObject;

use App\Authentication\Domain\ValueObject\FirstName;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FirstNameTest extends TestCase
{
    public function testValidFirstName(): void
    {
        $firstname = new FirstName('Jean');

        $this->assertSame('Jean', $firstname->value());
    }

    #[DataProvider(methodName: 'invalidFirstNameProvider')]
    public function testInvalidFirstName($firstName): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new FirstName($firstName);
    }

    public static function invalidFirstNameProvider(): array
    {
        return array_map(fn ($firstname) => [$firstname], [
            '',
            'J',
            '1Jean'
        ]);
    }
}
