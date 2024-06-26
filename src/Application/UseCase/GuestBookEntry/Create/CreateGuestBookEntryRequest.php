<?php

namespace App\Application\UseCase\GuestBookEntry\Create;

readonly class CreateGuestBookEntryRequest
{
    public function __construct(
        private string $author,
        private string $message,
    )
    {

    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}