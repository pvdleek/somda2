<?php
declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Entity\ForumForum;
use App\Entity\User;
use App\Generics\RoleGenerics;
use App\Helpers\ForumAuthorizationHelper;
use PHPUnit\Framework\TestCase;

class ForumAuthorizationHelperTest extends TestCase
{
    private ForumAuthorizationHelper $forum_authorization_helper;

    protected function setUp(): void
    {
        $this->forum_authorization_helper = new ForumAuthorizationHelper();
    }

    public function testMayViewPublicForumWithoutUser(): void
    {
        $forum = $this->createForum(ForumForum::TYPE_PUBLIC);

        $result = $this->forum_authorization_helper->mayView($forum, null);

        $this->assertTrue($result);
    }

    public function testMayViewPublicForumWithUser(): void
    {
        $forum = $this->createForum(ForumForum::TYPE_PUBLIC);
        $user = $this->createMock(User::class);

        $result = $this->forum_authorization_helper->mayView($forum, $user);

        $this->assertTrue($result);
    }

    public function testMayViewLoggedInForumWithoutUser(): void
    {
        $forum = $this->createForum(ForumForum::TYPE_LOGGED_IN);

        $result = $this->forum_authorization_helper->mayView($forum, null);

        $this->assertFalse($result);
    }

    public function testMayViewLoggedInForumWithUser(): void
    {
        $forum = $this->createForum(ForumForum::TYPE_LOGGED_IN);
        $user = $this->createMock(User::class);

        $result = $this->forum_authorization_helper->mayView($forum, $user);

        $this->assertTrue($result);
    }

    public function testMayViewArchiveForumWithUser(): void
    {
        $forum = $this->createForum(ForumForum::TYPE_ARCHIVE);
        $user = $this->createMock(User::class);

        $result = $this->forum_authorization_helper->mayView($forum, $user);

        $this->assertTrue($result);
    }

    public function testMayViewModeratorsOnlyForumWithModerator(): void
    {
        $user = $this->createMock(User::class);
        $forum = $this->createForum(ForumForum::TYPE_MODERATORS_ONLY, [$user]);

        $result = $this->forum_authorization_helper->mayView($forum, $user);

        $this->assertTrue($result);
    }

    public function testMayViewModeratorsOnlyForumWithAdmin(): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('hasRole')
            ->with(RoleGenerics::ROLE_ADMIN)
            ->willReturn(true);

        $forum = $this->createForum(ForumForum::TYPE_MODERATORS_ONLY);

        $result = $this->forum_authorization_helper->mayView($forum, $user);

        $this->assertTrue($result);
    }

    public function testMayViewModeratorsOnlyForumWithNormalUser(): void
    {
        $user = $this->createMock(User::class);
        $forum = $this->createForum(ForumForum::TYPE_MODERATORS_ONLY);

        $result = $this->forum_authorization_helper->mayView($forum, $user);

        $this->assertFalse($result);
    }

    public function testMayPostPublicForumWithUser(): void
    {
        $forum = $this->createForum(ForumForum::TYPE_PUBLIC);
        $user = $this->createMock(User::class);

        $result = $this->forum_authorization_helper->mayPost($forum, $user);

        $this->assertTrue($result);
    }

    public function testMayPostPublicForumWithoutUser(): void
    {
        $forum = $this->createForum(ForumForum::TYPE_PUBLIC);

        $result = $this->forum_authorization_helper->mayPost($forum, null);

        $this->assertFalse($result);
    }

    public function testMayPostArchiveForum(): void
    {
        $forum = $this->createForum(ForumForum::TYPE_ARCHIVE);
        $user = $this->createMock(User::class);

        $result = $this->forum_authorization_helper->mayPost($forum, $user);

        $this->assertFalse($result);
    }

    public function testMayPostLoggedInForumWithUser(): void
    {
        $forum = $this->createForum(ForumForum::TYPE_LOGGED_IN);
        $user = $this->createMock(User::class);

        $result = $this->forum_authorization_helper->mayPost($forum, $user);

        $this->assertTrue($result);
    }

    public function testMayPostModeratorsOnlyWithModerator(): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('hasRole')
            ->with(RoleGenerics::ROLE_ADMIN)
            ->willReturn(false);

        $forum = $this->createForum(ForumForum::TYPE_MODERATORS_ONLY, [$user]);

        $result = $this->forum_authorization_helper->mayPost($forum, $user);

        $this->assertTrue($result);
    }

    public function testUserIsModeratorReturnsTrueForModerator(): void
    {
        $user = $this->createMock(User::class);
        $user->method('hasRole')
            ->with(RoleGenerics::ROLE_ADMIN)
            ->willReturn(false);

        $forum = $this->createForum(ForumForum::TYPE_PUBLIC, [$user]);

        $result = $this->forum_authorization_helper->userIsModerator($forum, $user);

        $this->assertTrue($result);
    }

    public function testUserIsModeratorReturnsTrueForAdmin(): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('hasRole')
            ->with(RoleGenerics::ROLE_ADMIN)
            ->willReturn(true);

        $forum = $this->createForum(ForumForum::TYPE_PUBLIC);

        $result = $this->forum_authorization_helper->userIsModerator($forum, $user);

        $this->assertTrue($result);
    }

    public function testUserIsModeratorReturnsFalseForNormalUser(): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('hasRole')
            ->with(RoleGenerics::ROLE_ADMIN)
            ->willReturn(false);

        $forum = $this->createForum(ForumForum::TYPE_PUBLIC);

        $result = $this->forum_authorization_helper->userIsModerator($forum, $user);

        $this->assertFalse($result);
    }

    public function testUserIsModeratorReturnsFalseForNullUser(): void
    {
        $forum = $this->createForum(ForumForum::TYPE_PUBLIC);

        $result = $this->forum_authorization_helper->userIsModerator($forum, null);

        $this->assertFalse($result);
    }

    private function createForum(int $type, array $moderators = []): ForumForum
    {
        $forum = $this->createMock(ForumForum::class);
        $forum->type = $type;
        $forum->method('getModerators')->willReturn($moderators);

        return $forum;
    }
}
