<?php

namespace App\Authentication\Application\UseCase\Token\CreateToken;

use App\Authentication\Domain\Enum\TokenType;
use App\Authentication\Domain\ValueObject\UserId;

final readonly class CreateTokenCommand
{
    public function __construct(
        private UserId    $id,
        private TokenType $type,
    )
    {

    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function type(): TokenType
    {
        return $this->type;
    }
}
