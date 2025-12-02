<?php

namespace App\Tests\Authentication\Domain\ValueObject;

use App\Authentication\Domain\ValueObject\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testValidEmail(): void
    {
        $email = new Email('john@example.com');

        $this->assertSame('john@example.com', $email->value());
    }

    public function testInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Email('not-a-valid-email');
    }
}
