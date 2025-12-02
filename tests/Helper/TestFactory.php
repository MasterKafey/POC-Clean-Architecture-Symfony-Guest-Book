<?php

namespace App\Tests\Helper;

use App\Authentication\Domain\Entity\Token;
use App\Authentication\Domain\Entity\User;
use App\Authentication\Domain\Enum\TokenType;
use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\FirstName;
use App\Authentication\Domain\ValueObject\LastName;
use App\Authentication\Domain\ValueObject\PasswordHash;
use App\Authentication\Domain\ValueObject\TokenId;
use App\Authentication\Domain\ValueObject\TokenValue;
use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Domain\Entity\Comment;
use App\Comment\Domain\ValueObject\CommentId;

class TestFactory
{
    public static function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function makeUser(
        bool    $banned = false,
        bool    $admin = true,
        ?string $email = null,
        bool    $validated = true,
    ): User
    {
        return new User(
            new UserId(self::uuid()),
            new FirstName('John'),
            new LastName('Doe'),
            new Email($email ?? 'john@example.com'),
            new PasswordHash(password_hash('secret', PASSWORD_BCRYPT)),
            $admin,
            $banned,
            $validated
        );
    }

    public static function makeComment(
        UserId $userId,
        bool   $blocked = false,
        string $message = 'message'
    ): Comment
    {
        return new Comment(
            new CommentId(self::uuid()),
            $userId,
            $message,
            new \DateTimeImmutable(),
            $blocked
        );
    }

    public static function makeValidToken(User $user, string $tokenValue = null): Token
    {
        return new Token(
            new TokenId(self::uuid()),
            $user->getId(),
            TokenType::AUTHENTICATION,
            new TokenValue($tokenValue ?? self::uuid()),
            (new \DateTimeImmutable())->modify('+10 minutes')
        );
    }
}
