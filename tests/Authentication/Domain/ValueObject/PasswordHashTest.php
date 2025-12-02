<?php

namespace App\Tests\Authentication\Domain\ValueObject;

use App\Authentication\Domain\ValueObject\PasswordHash;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PasswordHashTest extends TestCase
{
    public function testValidPasswordHash(): void
    {
        $plainPassword = 'test';

        $passwordHash = new PasswordHash(password_hash($plainPassword, PASSWORD_BCRYPT));

        $this->assertTrue(password_verify($plainPassword, $passwordHash->value()));
    }

    #[DataProvider(methodName: 'invalidPasswordHashProvider')]
    public function testInvalidPasswordHash(string $passwordValue): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new PasswordHash($passwordValue);
    }

    public static function invalidPasswordHashProvider(): array
    {
        return array_map(fn (string $password) => [$password], [
            '',
            bin2hex(random_bytes(5)),
            "My password",
        ]);
    }
}
