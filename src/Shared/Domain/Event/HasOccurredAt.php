<?php

namespace App\Shared\Domain\Event;

trait HasOccurredAt
{
    private readonly \DateTimeImmutable $occurredAt;

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
