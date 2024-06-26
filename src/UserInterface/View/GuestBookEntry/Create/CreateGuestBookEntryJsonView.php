<?php

namespace App\UserInterface\View\GuestBookEntry\Create;

use App\Application\UseCase\GuestBookEntry\Create\CreateGuestBookEntryResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

readonly class CreateGuestBookEntryJsonView implements CreateGuestBookEntryViewInterface
{
    public function __construct(
        private SerializerInterface $serializer
    )
    {

    }

    public function generateResponse(CreateGuestBookEntryResponse $response): Response
    {
        return new JsonResponse(data: $this->serializer->serialize($response, 'json'), json: true);
    }
}