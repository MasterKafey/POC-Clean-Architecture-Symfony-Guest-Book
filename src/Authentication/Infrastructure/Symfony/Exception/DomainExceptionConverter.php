<?php

namespace App\Authentication\Infrastructure\Symfony\Exception;

use App\Authentication\Domain\Exception\EmailAlreadyUsedException;
use App\Authentication\Domain\Exception\InvalidCredentialsException;
use App\Authentication\Domain\Exception\TokenExpiredException;
use App\Authentication\Domain\Exception\TokenNotFoundException;
use App\Authentication\Domain\Exception\UnauthorizedException;
use App\Authentication\Domain\Exception\UserAlreadyBannedException;
use App\Authentication\Domain\Exception\UserAlreadyValidatedException;
use App\Authentication\Domain\Exception\UserBannedException;
use App\Authentication\Domain\Exception\UserNotBannedException;
use App\Authentication\Domain\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final readonly class DomainExceptionConverter
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $response = match (true) {
            $exception instanceof UserNotFoundException
            => $this->convertToError($exception->getMessage(), Response::HTTP_NOT_FOUND),

            $exception instanceof EmailAlreadyUsedException ||
            $exception instanceof UserAlreadyBannedException ||
            $exception instanceof UserAlreadyValidatedException ||
            $exception instanceof UserNotBannedException
            => $this->convertToError($exception->getMessage(), Response::HTTP_BAD_REQUEST),

            $exception instanceof InvalidCredentialsException ||
            $exception instanceof TokenExpiredException ||
            $exception instanceof TokenNotFoundException ||
            $exception instanceof UnauthorizedException
            => $this->convertToError($exception->getMessage(), Response::HTTP_UNAUTHORIZED),

            $exception instanceof UserBannedException
            => $this->convertToError($exception->getMessage(), Response::HTTP_FORBIDDEN),

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
