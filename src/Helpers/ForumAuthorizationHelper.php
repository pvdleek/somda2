<?php

namespace App\Helpers;

use App\Entity\ForumForum;
use App\Entity\User;
use App\Generics\RoleGenerics;

class ForumAuthorizationHelper
{
    public function mayView(ForumForum $forum, ?User $user = null): bool
    {
        if ($forum->type === ForumForum::TYPE_PUBLIC || (null !== $user) && $user->hasRole(RoleGenerics::ROLE_ADMIN)) {
            return true;
        }
        if (\in_array($forum->type, [ForumForum::TYPE_LOGGED_IN, ForumForum::TYPE_ARCHIVE])) {
            return null !== $user;
        }
        return \in_array($user, $forum->getModerators());
    }

    public function mayPost(ForumForum $forum, ?User $user = null): bool
    {
        if (!$this->mayView($forum, $user) || $forum->type === ForumForum::TYPE_ARCHIVE) {
            return false;
        }
        if (\in_array($forum->type, [ForumForum::TYPE_PUBLIC, ForumForum::TYPE_LOGGED_IN])) {
            return null !== $user;
        }
        return \in_array($user, $forum->getModerators()) || $user->hasRole(RoleGenerics::ROLE_ADMIN);
    }

    public function userIsModerator(ForumForum $forum, ?User $user = null): bool
    {
        return null !== $user
            && (\in_array($user, $forum->getModerators()) || $user->hasRole(RoleGenerics::ROLE_ADMIN));
    }
}
