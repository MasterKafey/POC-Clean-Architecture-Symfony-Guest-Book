<?php

namespace App\Authentication\Application\UseCase\Token\CreateToken;

use App\Authentication\Domain\Entity\Token;
use App\Authentication\Domain\Exception\UserNotFoundException;
use App\Authentication\Domain\Repository\TokenRepositoryInterface;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\ValueObject\TokenId;
use App\Authentication\Domain\ValueObject\TokenValue;
use App\Shared\Domain\Port\UuidGeneratorPort;

final readonly class CreateTokenHandler
{
    public function __construct(
        private TokenRepositoryInterface $tokenRepository,
        private UserRepositoryInterface  $userRepository,
        private UuidGeneratorPort        $uuidGeneratorPort
    )
    {

    }

    public function __invoke(
        CreateTokenCommand $command,
    ): Token
    {
        $user = $this->userRepository->findById($command->id());

        if (null === $user) {
            throw new UserNotFoundException($command->id());
        }

        $token = new Token(
            new TokenId($this->uuidGeneratorPort->generate()),
            $user->getId(),
            $command->type(),
            TokenValue::generate(),
            (new \DateTimeImmutable())->modify('+15 minutes')
        );

        $this->tokenRepository->save($token);
        return $token;
    }
}
