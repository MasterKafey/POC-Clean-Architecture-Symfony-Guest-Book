<?php

namespace App\Tests\Authentication\Application\UseCase\Token\AuthenticateUserByToken;

use App\Authentication\Application\UseCase\Token\AuthenticateUserByToken\AuthenticateUserByTokenHandler;
use App\Authentication\Application\UseCase\Token\AuthenticateUserByToken\AuthenticateUserByTokenQuery;
use App\Authentication\Domain\Entity\Token;
use App\Authentication\Domain\Enum\TokenType;
use App\Authentication\Domain\Event\UserAuthenticatedByToken;
use App\Authentication\Domain\Exception\TokenExpiredException;
use App\Authentication\Domain\Exception\UserBannedException;
use App\Authentication\Domain\Exception\UserNotFoundException;
use App\Authentication\Domain\Exception\UserNotValidatedException;
use App\Authentication\Domain\Repository\TokenRepositoryInterface;
use App\Authentication\Domain\Repository\UserRepositoryInterface;
use App\Authentication\Domain\ValueObject\TokenId;
use App\Authentication\Domain\ValueObject\TokenValue;
use App\Shared\Domain\Port\EventBusPort;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\TestCase;

class AuthenticateUserByTokenHandlerTest extends TestCase
{
    public function testAuthenticateSuccess()
    {
        $user = TestFactory::makeUser();
        $token = TestFactory::makeValidToken($user);

        $tokenRepo = $this->createMock(TokenRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $tokenRepo->method('findTokenByValue')->willReturn($token);
        $userRepo->method('findById')->willReturn($user);

        $eventBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(UserAuthenticatedByToken::class));

        $handler = new AuthenticateUserByTokenHandler(
            $tokenRepo,
            $userRepo,
            $eventBus
        );

        $tokenValue = TestFactory::uuid();
        $query = new AuthenticateUserByTokenQuery(new TokenValue($tokenValue));
        $result = $handler($query);

        $this->assertSame($user, $result->getUser());
    }

    public function testThrowsOnExpiredToken()
    {
        $user = TestFactory::makeUser();
        $tokenValue = new TokenValue(TestFactory::uuid());
        $expiredToken = new Token(
            new TokenId(TestFactory::uuid()),
            $user->getId(),
            TokenType::AUTHENTICATION,
            $tokenValue,
            (new \DateTimeImmutable())->modify('-10 minutes')
        );

        $tokenRepo = $this->createMock(TokenRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $tokenRepo->method('findTokenByValue')->willReturn($expiredToken);

        $handler = new AuthenticateUserByTokenHandler(
            $tokenRepo,
            $userRepo,
            $eventBus
        );

        $this->expectException(TokenExpiredException::class);

        $handler(new AuthenticateUserByTokenQuery($tokenValue));
    }

    public function testThrowsOnMissingUser()
    {
        $user = TestFactory::makeUser();
        $token = TestFactory::makeValidToken($user);

        $tokenRepo = $this->createMock(TokenRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $tokenRepo->method('findTokenByValue')->willReturn($token);
        $userRepo->method('findById')->willReturn(null);

        $handler = new AuthenticateUserByTokenHandler(
            $tokenRepo,
            $userRepo,
            $eventBus
        );

        $this->expectException(UserNotFoundException::class);

        $handler(new AuthenticateUserByTokenQuery(new TokenValue(TestFactory::uuid())));
    }

    public function testThrowsOnBannedUser()
    {
        $bannedUser = TestFactory::makeUser(banned: true);
        $token = TestFactory::makeValidToken($bannedUser);

        $tokenRepo = $this->createMock(TokenRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $tokenRepo->method('findTokenByValue')->willReturn($token);
        $userRepo->method('findById')->willReturn($bannedUser);

        $handler = new AuthenticateUserByTokenHandler(
            $tokenRepo,
            $userRepo,
            $eventBus
        );

        $this->expectException(UserBannedException::class);

        $handler(new AuthenticateUserByTokenQuery(new TokenValue($token->getValue())));
    }

    public function testThrowsOnNotValidatedUser()
    {
        $notValidatedUser = TestFactory::makeUser(validated: false);
        $token = TestFactory::makeValidToken($notValidatedUser);

        $tokenRepo = $this->createMock(TokenRepositoryInterface::class);
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $eventBus = $this->createMock(EventBusPort::class);

        $tokenRepo->method('findTokenByValue')->willReturn($token);
        $userRepo->method('findById')->willReturn($notValidatedUser);

        $handler = new AuthenticateUserByTokenHandler(
            $tokenRepo,
            $userRepo,
            $eventBus
        );

        $this->expectException(UserNotValidatedException::class);

        $handler(new AuthenticateUserByTokenQuery(new TokenValue($token->getValue())));
    }
}
