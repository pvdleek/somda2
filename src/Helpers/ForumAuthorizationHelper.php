<?php

namespace App\Helpers;

use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Entity\User;

class ForumAuthorizationHelper
{
    /**
     * @param ForumForum $forum
     * @param User|null $user
     * @return bool
     */
    public function mayView(ForumForum $forum, User $user = null): bool
    {
        if ($forum->type === ForumForum::TYPE_PUBLIC) {
            return true;
        }
        if (in_array($forum->type, [ForumForum::TYPE_LOGGED_IN, ForumForum::TYPE_ARCHIVE])) {
            return !is_null($user);
        }
        return in_array($user, $forum->getModerators());
    }

    /**
     * @param ForumForum $forum
     * @param User|null $user
     * @return bool
     */
    public function mayPost(ForumForum $forum, User $user = null): bool
    {
        if (!$this->mayView($forum, $user) || $forum->type === ForumForum::TYPE_ARCHIVE) {
            return false;
        }
        if (in_array($forum->type, [ForumForum::TYPE_PUBLIC, ForumForum::TYPE_LOGGED_IN])) {
            return !is_null($user);
        }
        return in_array($user, $forum->getModerators());
    }

    /**
     * @param ForumDiscussion $discussion
     * @param User|null $user
     * @return bool
     */
    public function userIsModerator(ForumDiscussion $discussion, User $user = null): bool
    {
        return !is_null($user) && in_array($user, $discussion->forum->getModerators());
    }
}
