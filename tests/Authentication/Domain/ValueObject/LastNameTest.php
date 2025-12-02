<?php

namespace App\Tests\Authentication\Domain\ValueObject;

use App\Authentication\Domain\ValueObject\LastName;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LastNameTest extends TestCase
{
    public function testValidLastName(): void
    {
        $lastName = new LastName('Marius');

        $this->assertSame('Marius', $lastName->value());
    }

    #[DataProvider(methodName: 'invalidLastNameProvider')]
    public function testInvalidLastName($lastName): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new LastName($lastName);
    }

    public static function invalidLastNameProvider(): array
    {
        return array_map(fn ($lastName) => [$lastName], [
            '',
            'M',
            '1Marius'
        ]);
    }
}
