<?php

namespace App\Shared\Infrastructure\Adapter\UuidGenerator;

use App\Shared\Domain\Port\UuidGeneratorPort;
use Symfony\Component\Uid\Uuid;

class SymfonyUuidGenerator implements UuidGeneratorPort
{
    public function generate(): string
    {
        return Uuid::v4()->toRfc4122();
    }
}
