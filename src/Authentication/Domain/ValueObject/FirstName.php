<?php

namespace App\Authentication\Domain\ValueObject;

final readonly class FirstName
{
    private string $value;

    public function __construct(
        string $value,
    )
    {
        $normalized = trim($value);

        if ($normalized === '') {
            throw new \InvalidArgumentException('First name cannot be empty.');
        }

        $length = mb_strlen($normalized);
        if ($length < 2 || $length > 50) {
            throw new \InvalidArgumentException('First name must be between 2 and 50 characters.');
        }

        if (!preg_match("/^[\p{L}][\p{L}\p{M}'\- ]*$/u", $normalized)) {
            throw new \InvalidArgumentException('First name contains invalid characters.');
        }

        $this->value = $this->normalize($value);
    }

    private function normalize(string $value): string
    {
        return mb_strtoupper(mb_substr($value, 0, 1)) . mb_strtolower(mb_substr($value, 1));
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
