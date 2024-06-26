<?php

namespace App\UserInterface\View\GuestBookEntry\List;

use App\Application\UseCase\GuestBookEntry\List\ListGuestBookEntryResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

readonly class ListGuestBookEntryJsonView implements ListGuestBookEntryViewInterface
{
    public function __construct(
        private SerializerInterface $serializer
    )
    {
    }

    public function generateResponse(ListGuestBookEntryResponse $response): Response
    {
        return new JsonResponse(data: $this->serializer->serialize($response, 'json'), json: true);
    }
}