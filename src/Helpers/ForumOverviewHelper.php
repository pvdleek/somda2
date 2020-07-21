<?php

namespace App\Helpers;

use App\Entity\ForumForum;
use App\Traits\SortTrait;
use Doctrine\Persistence\ManagerRegistry;

class ForumOverviewHelper
{
    use SortTrait;

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var ForumAuthorizationHelper
     */
    private ForumAuthorizationHelper $forumAuthHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     * @param ForumAuthorizationHelper $forumAuthHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        UserHelper $userHelper,
        ForumAuthorizationHelper $forumAuthHelper
    ) {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
        $this->forumAuthHelper = $forumAuthHelper;
    }

    /**
     * @return array
     */
    public function getCategoryArray(): array
    {
        $categories = [];
        $forums = $this->doctrine->getRepository(ForumForum::class)->findAll($this->userHelper->getUser());
        foreach ($forums as $forum) {
            if (!isset($categories[$forum['categoryId']])) {
                $categories[$forum['categoryId']] = [
                    'id' => $forum['categoryId'],
                    'name' => $forum['categoryName'],
                    'order' => $forum['categoryOrder'],
                    'forums' => [],
                ];
            }

            if ((int)$forum['type'] === ForumForum::TYPE_MODERATORS_ONLY) {
                if (!$this->userHelper->userIsLoggedIn()) {
                    continue;
                }
                /**
                 * @var ForumForum $forumEntity
                 */
                $forumEntity = $this->doctrine->getRepository(ForumForum::class)->find($forum['id']);
                if (!$this->forumAuthHelper->userIsModerator($forumEntity, $this->userHelper->getUser())) {
                    continue;
                }
            }

            $categories[$forum['categoryId']]['forums'][] = [
                'id' => $forum['id'],
                'name' => $forum['name'],
                'order' => $forum['order'],
                'numberOfDiscussions' => $forum['numberOfDiscussions'],
                'forum_read' => $forum['forum_read'],
            ];
        }
        return $categories;
    }
}
