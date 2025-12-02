<?php

namespace App\Tests\Authentication\Application\UseCase\Token\RefreshToken;

use App\Authentication\Application\UseCase\Token\RefreshToken\RefreshTokenCommand;
use App\Authentication\Application\UseCase\Token\RefreshToken\RefreshTokenHandler;
use App\Authentication\Domain\Entity\Token;
use App\Authentication\Domain\Exception\TokenExpiredException;
use App\Authentication\Domain\Repository\TokenRepositoryInterface;
use App\Authentication\Domain\ValueObject\TokenValue;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\TestCase;

final class RefreshTokenHandlerTest extends TestCase
{
    public function testRefreshTokenSuccessfully()
    {
        $tokenValue = new TokenValue(TestFactory::uuid());
        $user = TestFactory::makeUser();
        $token = TestFactory::makeValidToken($user, $tokenValue->value());

        $repo = $this->createMock(TokenRepositoryInterface::class);

        $repo->method('findTokenByValue')->willReturn($token);

        $repo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Token $updatedToken) use ($token) {
                return $updatedToken->getValue()->value() === $token->getValue()->value()
                    && $updatedToken->getExpiresAt() > new \DateTimeImmutable();
            }))
            ->willReturnCallback(fn(Token $t) => $t);

        $handler = new RefreshTokenHandler($repo);

        $command = new RefreshTokenCommand($tokenValue);
        $handler($command);

        $this->assertTrue(
            $token->getExpiresAt() > new \DateTimeImmutable()
        );
    }

    public function testThrowsIfTokenDoesNotExist()
    {
        $repo = $this->createMock(TokenRepositoryInterface::class);
        $repo->method('findTokenByValue')->willReturn(null);

        $handler = new RefreshTokenHandler($repo);

        $this->expectException(TokenExpiredException::class);

        $handler(new RefreshTokenCommand(new TokenValue(TestFactory::uuid())));
    }
}
