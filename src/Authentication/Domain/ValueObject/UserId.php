<?php

namespace App\Authentication\Domain\ValueObject;

final readonly class UserId
{
    public function __construct(
        private string $value
    )
    {
        $this->assertValidUuid($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value();
    }

    private function assertValidUuid(string $value): void
    {
        if (!preg_match(
            '/^[0-9a-fA-F]{8}-' .
            '[0-9a-fA-F]{4}-' .
            '[0-9a-fA-F]{4}-' .
            '[0-9a-fA-F]{4}-' .
            '[0-9a-fA-F]{12}$/',
            $value
        )) {
            throw new \InvalidArgumentException("Invalid UUID format for TokenId: {$value}");
        }
    }
}
