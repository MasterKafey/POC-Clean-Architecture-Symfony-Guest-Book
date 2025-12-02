<?php

namespace App\Tests\Authentication\Application\UseCase\Token\CreateToken;

use App\Authentication\Application\UseCase\Token\CreateToken\CreateTokenCommand;
use App\Authentication\Application\UseCase\Token\CreateToken\CreateTokenHandler;
use App\Authentication\Domain\Entity\Token;
use App\Authentication\Domain\Enum\TokenType;
use App\Authentication\Domain\Exception\UserNotFoundException;
use App\Authentication\Domain\Repository\TokenRepositoryInterface;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\ValueObject\UserId;
use App\Shared\Domain\Port\UuidGeneratorPort;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\TestCase;

final class CreateTokenHandlerTest extends TestCase
{
    public function testCreateTokenSuccessfully()
    {
        $user = TestFactory::makeUser();
        $uuidGenerated = TestFactory::uuid();

        $tokenRepo = $this->createMock(TokenRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $uuidPort = $this->createMock(UuidGeneratorPort::class);

        $userRepo->method('findById')->willReturn($user);
        $uuidPort->method('generate')->willReturn($uuidGenerated);

        $tokenRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($token) use ($user) {
                return $token instanceof Token
                    && $token->getUserId()->value() === $user->getId()->value()
                    && $token->getTokenType() === TokenType::AUTHENTICATION
                    && $token->getExpiresAt() > new \DateTimeImmutable();
            }))
            ->willReturnCallback(function (Token $token) {
                return $token;
            });

        $handler = new CreateTokenHandler(
            $tokenRepo,
            $userRepo,
            $uuidPort
        );

        $command = new CreateTokenCommand(
            $user->getId(),
            TokenType::AUTHENTICATION
        );

        $result = $handler($command);

        $this->assertEquals($user->getId(), $result->getUserId());
        $this->assertEquals(TokenType::AUTHENTICATION, $result->getTokenType());
        $this->assertNotEmpty($result->getValue()->value());

        $this->assertTrue(
            $result->getExpiresAt() > new \DateTimeImmutable()
        );
    }

    public function testThrowsWhenUserDoesNotExist()
    {
        $nonExistingUserId = new UserId(TestFactory::uuid());

        $tokenRepo = $this->createMock(TokenRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $uuidPort = $this->createMock(UuidGeneratorPort::class);

        $userRepo->method('findById')
            ->willReturn(null);

        $handler = new CreateTokenHandler(
            $tokenRepo,
            $userRepo,
            $uuidPort
        );

        $command = new CreateTokenCommand(
            $nonExistingUserId,
            TokenType::AUTHENTICATION
        );

        $this->expectException(UserNotFoundException::class);

        $handler($command);
    }
}
