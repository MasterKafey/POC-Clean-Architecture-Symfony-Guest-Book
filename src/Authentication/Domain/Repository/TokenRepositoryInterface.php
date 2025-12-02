<?php

namespace App\Authentication\Domain\Repository;

use App\Authentication\Domain\Entity\Token;
use App\Authentication\Domain\ValueObject\TokenValue;

interface TokenRepositoryInterface
{
    public function save(Token $token): Token;

    public function findTokenByValue(TokenValue $value): ?Token;

    public function remove(Token $token);
}
