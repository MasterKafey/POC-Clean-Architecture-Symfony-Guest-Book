<?php

namespace App\Authentication\Domain\ValueObject;

final readonly class Email
{
    public function __construct(
        private string $value,
    )
    {
        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf('Invalid email address: %s', $this->value));
        }
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
