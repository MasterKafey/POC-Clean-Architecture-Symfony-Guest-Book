<?php

namespace App\Tests\Shared\Infrastructure\Adapter\UuidGenerator;

use App\Shared\Infrastructure\Adapter\UuidGenerator\SymfonyUuidGenerator;
use PHPUnit\Framework\TestCase;

final class SymfonyUuidGeneratorTest extends TestCase
{
    public function testReturnsValidUuid(): void
    {
        $generator = new SymfonyUuidGenerator();
        $uuid = $generator->generate();

        $this->assertIsString($uuid);
        $this->assertNotEmpty($uuid);

        // Format UUID v4 RFC4122 classique
        $regex = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
        $this->assertMatchesRegularExpression($regex, $uuid);
    }

    public function testGenerateProducesDifferentValues(): void
    {
        $generator = new SymfonyUuidGenerator();

        $uuid1 = $generator->generate();
        $uuid2 = $generator->generate();

        $this->assertNotSame($uuid1, $uuid2);
    }
}
