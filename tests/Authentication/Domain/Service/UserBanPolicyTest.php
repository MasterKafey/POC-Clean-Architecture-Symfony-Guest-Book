<?php

namespace App\Tests\Authentication\Domain\Service;

use App\Authentication\Domain\Entity\User;
use App\Authentication\Domain\Service\UserBanPolicy;
use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\FirstName;
use App\Authentication\Domain\ValueObject\LastName;
use App\Authentication\Domain\ValueObject\PasswordHash;
use App\Authentication\Domain\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

class UserBanPolicyTest extends TestCase
{
    private function uuid(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private function makeUser(bool $isAdmin = false, bool $isBanned = false): User
    {
        return new User(
            new UserId($this->uuid()),
            new FirstName('John'),
            new LastName('Doe'),
            new Email('john@example.com'),
            new PasswordHash(password_hash('password', PASSWORD_BCRYPT)),
            $isAdmin,
            $isBanned
        );
    }

    public function testUserCannotBanThemself()
    {
        $admin = $this->makeUser(isAdmin: true);

        $target = $admin;

        $this->assertFalse(UserBanPolicy::canToggleBan($target, $admin));
    }

    public function testAdminCanBanRegularUser()
    {
        $admin = $this->makeUser(isAdmin: true);
        $target = $this->makeUser();

        $this->assertTrue(UserBanPolicy::canToggleBan($target, $admin));
    }

    public function testNonAdminCannotBan()
    {
        $initiator = $this->makeUser();
        $target = $this->makeUser();

        $this->assertFalse(UserBanPolicy::canToggleBan($target, $initiator));
    }

    public function testAdminCannotBanIfBanned()
    {
        $bannedAdmin = $this->makeUser(isAdmin: true, isBanned: true);
        $target = $this->makeUser();

        $this->assertFalse(UserBanPolicy::canToggleBan($target, $bannedAdmin));
    }

    public function testAdminCanBanAnotherAdmin()
    {
        $admin1 = $this->makeUser(isAdmin: true);
        $admin2 = $this->makeUser(isAdmin: true);

        $this->assertTrue(UserBanPolicy::canToggleBan($admin2, $admin1));
    }
}
