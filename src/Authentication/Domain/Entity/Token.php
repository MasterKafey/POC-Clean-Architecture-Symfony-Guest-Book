<?php

namespace App\Authentication\Domain\Entity;

use App\Authentication\Domain\Enum\TokenType;
use App\Authentication\Domain\Exception\TokenExpiredException;
use App\Authentication\Domain\ValueObject\TokenId;
use App\Authentication\Domain\ValueObject\TokenValue;
use App\Authentication\Domain\ValueObject\UserId;

final class Token
{
    public function __construct(
        private readonly TokenId    $id,
        private readonly UserId     $userId,
        private readonly TokenType  $tokenType,
        private readonly TokenValue $value,
        private \DateTimeImmutable  $expiresAt,
    )
    {

    }

    public function getId(): TokenId
    {
        return $this->id;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getTokenType(): TokenType
    {
        return $this->tokenType;
    }

    public function getValue(): TokenValue
    {
        return $this->value;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTime();
    }

    public function refreshExpirationDate(\DateTimeImmutable $newExpiration): void
    {
        if ($this->isExpired()) {
            throw new TokenExpiredException();
        }

        if ($newExpiration < $this->expiresAt) {
            throw new \InvalidArgumentException('New expiration date must be greater than current expiration.');
        }

        $this->expiresAt = $newExpiration;
    }
}
