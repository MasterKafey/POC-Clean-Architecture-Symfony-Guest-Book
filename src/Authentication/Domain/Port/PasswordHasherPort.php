<?php

namespace App\Authentication\Domain\Port;

use App\Authentication\Domain\ValueObject\PasswordHash;

interface PasswordHasherPort
{
    public function hash(string $password): PasswordHash;

    public function verify(PasswordHash $hash, string $password): bool;
}
