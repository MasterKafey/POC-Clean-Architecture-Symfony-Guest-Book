<?php

namespace App\Authentication\Infrastructure\Symfony\Adapter;

use App\Authentication\Domain\Port\PasswordHasherPort;
use App\Authentication\Domain\ValueObject\PasswordHash;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

final readonly class PasswordHasherAdapter implements PasswordHasherPort
{
    public function __construct(
        private PasswordHasherFactoryInterface $factory
    )
    {
    }

    public function hash(string $password): PasswordHash
    {
        $hasher = $this->factory->getPasswordHasher('common');

        return new PasswordHash($hasher->hash($password));
    }

    public function verify(PasswordHash $hash, string $password): bool
    {
        $hasher = $this->factory->getPasswordHasher('common');

        return $hasher->verify($hash, $password);
    }
}
