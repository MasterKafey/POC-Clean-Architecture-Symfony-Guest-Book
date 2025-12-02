<?php

namespace App\Tests\Comment\Domain\Service;

use App\Authentication\Domain\Entity\User;
use App\Authentication\Domain\ValueObject\Email;
use App\Authentication\Domain\ValueObject\FirstName;
use App\Authentication\Domain\ValueObject\LastName;
use App\Authentication\Domain\ValueObject\PasswordHash;
use App\Authentication\Domain\ValueObject\UserId;
use App\Comment\Domain\Entity\Comment;
use App\Comment\Domain\ValueObject\CommentId;
use App\Comment\Domain\Service\CommentPolicy;
use App\Tests\Helper\TestFactory;
use PHPUnit\Framework\TestCase;

class CommentPolicyTest extends TestCase
{
    public function testCanToggleBlockedReturnsFalseForBannedUser()
    {

        $adminUser = TestFactory::makeUser(true);
        $commentOwner = TestFactory::makeUser(false, false);
        $comment = TestFactory::makeComment($commentOwner->getId());

        $this->assertFalse(CommentPolicy::canToggleBlock($comment, $adminUser));
    }

    public function testCanToggleBlockedReturnsTrueIfAdminAndNotBanned()
    {
        $adminUser = TestFactory::makeUser();
        $commentOwner = TestFactory::makeUser();
        $comment = TestFactory::makeComment($commentOwner->getId());

        $this->assertTrue(CommentPolicy::canToggleBlock($comment, $adminUser));
    }

    public function testCanToggleBlockedReturnsFalseIfUserNotAdmin()
    {
        $user = TestFactory::makeUser(admin: false);
        $owner = TestFactory::makeUser(admin: false);
        $comment = TestFactory::makeComment($owner->getId());

        $this->assertFalse(CommentPolicy::canToggleBlock($comment, $user));
    }

    public function testUserCannotDeleteOwnBlockedComment()
    {
        $user = TestFactory::makeUser(admin: false);
        $comment = TestFactory::makeComment($user->getId(), true);

        $this->assertFalse(CommentPolicy::canDelete($comment, $user));
    }

    public function testUserCanDeleteOwnNonBlockedComment()
    {
        $user = TestFactory::makeUser(admin: false);
        $comment = TestFactory::makeComment($user->getId());

        $this->assertTrue(CommentPolicy::canDelete($comment, $user));
    }

    public function testAdminCanDeleteComment()
    {
        $admin = TestFactory::makeUser();
        $owner = TestFactory::makeUser(admin: false);
        $comment = TestFactory::makeComment($owner->getId());

        $this->assertTrue(CommentPolicy::canDelete($comment, $admin));
    }

    public function testBannedUserCannotDelete()
    {
        $user = TestFactory::makeUser(banned: true, admin: false);
        $comment = TestFactory::makeComment($user->getId());

        $this->assertFalse(CommentPolicy::canDelete($comment, $user));
    }

    public function testAdminCanUpdateAlways()
    {
        $admin = TestFactory::makeUser();
        $owner = TestFactory::makeUser(admin: false);
        $comment = TestFactory::makeComment($owner->getId());

        $this->assertTrue(CommentPolicy::canUpdate($comment, $admin));
    }

    public function testAuthorCanUpdateOnlyIfNotBlocked()
    {
        $user = TestFactory::makeUser(admin: false);
        $comment = TestFactory::makeComment($user->getId());

        $this->assertTrue(CommentPolicy::canUpdate($comment, $user));
    }

    public function testAuthorCannotUpdateIfBlocked()
    {
        $user = TestFactory::makeUser(admin: false);
        $comment = TestFactory::makeComment($user->getId(), blocked: true);

        $this->assertFalse(CommentPolicy::canUpdate($comment, $user));
    }

    public function testBannedUserCannotUpdate()
    {
        $user = TestFactory::makeUser(banned: true, admin: false);
        $comment = TestFactory::makeComment($user->getId());

        $this->assertFalse(CommentPolicy::canUpdate($comment, $user));
    }
}
