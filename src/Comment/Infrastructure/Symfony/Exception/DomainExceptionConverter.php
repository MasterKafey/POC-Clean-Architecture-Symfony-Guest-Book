<?php

namespace App\Comment\Infrastructure\Symfony\Exception;

use App\Comment\Domain\Event\CommentBlocked;
use App\Comment\Domain\Exception\CommentAlreadyBlockedException;
use App\Comment\Domain\Exception\CommentNotBlockedException;
use App\Comment\Domain\Exception\CommentNotFoundException;
use App\Comment\Domain\Exception\UnauthorizedException;
use App\Comment\Domain\Exception\UserCommentAlreadyExists;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class DomainExceptionConverter
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $response = match (true) {
            $exception instanceof CommentNotFoundException
            => $this->convertToError($exception->getMessage(), Response::HTTP_NOT_FOUND),

            $exception instanceof UnauthorizedException
            => $this->convertToError($exception->getMessage(), Response::HTTP_FORBIDDEN),

            $exception instanceof CommentAlreadyBlockedException ||
            $exception instanceof CommentNotBlockedException ||
            $exception instanceof UserCommentAlreadyExists
            => $this->convertToError($exception->getMessage(), Response::HTTP_CONFLICT),

            default => null
        };

        if ($response !== null) {
            $event->setResponse($response);
        }
    }

    private function convertToError(string $message, int $status): JsonResponse
    {
        return new JsonResponse(['error' => $message], $status);
    }
}
