<?php

namespace App\Comment\Application\UseCase\Comment\GetComments;

final readonly class GetCommentsResult
{
    public function __construct(
        private array $comments
    )
    {

    }

    public function comments(): array
    {
        return $this->comments;
    }
}
