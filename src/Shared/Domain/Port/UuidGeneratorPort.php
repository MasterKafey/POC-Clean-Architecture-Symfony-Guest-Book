<?php

namespace App\Shared\Domain\Port;

interface UuidGeneratorPort
{
    public function generate(): string;
}
