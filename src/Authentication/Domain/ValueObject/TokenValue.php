<?php

namespace App\Authentication\Domain\ValueObject;

class TokenValue
{
    private string $value;

    public function __construct(
        string $value
    )
    {
        $value = trim($value);

        if ($value === '') {
            throw new \InvalidArgumentException('Token value cannot be empty');
        }

        if (strlen($value) < 20) {
            throw new \InvalidArgumentException('Token value cannot be less than 20 characters');
        }

        if (!preg_match('/^[A-Za-z0-9\-_]+$/', $value)) {
            throw new \InvalidArgumentException('Token value contains invalid characters');
        }

        $this->value = $value;
    }

    public static function generate(): TokenValue
    {
        return new self(bin2hex(random_bytes(32)));
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
