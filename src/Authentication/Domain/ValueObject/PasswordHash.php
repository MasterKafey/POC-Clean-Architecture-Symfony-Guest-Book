<?php

namespace App\Authentication\Domain\ValueObject;

final readonly class PasswordHash
{
    private string $password;

    public function __construct(string $hash)
    {
        $hash = trim($hash);

        if ($hash === '') {
            throw new \InvalidArgumentException('Password hash cannot be empty.');
        }

        if (strlen($hash) < 20) {
            throw new \InvalidArgumentException('Password hash appears invalid (too short).');
        }

        if (preg_match('/\s/', $hash)) {
            throw new \InvalidArgumentException('Password hash cannot contains spaces.');
        }

        $this->password = $hash;
    }

    public function value(): string
    {
        return $this->password;
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
