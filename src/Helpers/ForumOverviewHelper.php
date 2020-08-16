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
        $forums = $this->doctrine->getRepository(ForumForum::class)->findAll();
        foreach ($forums as $forum) {
            if (!isset($categories[$forum['categoryId']])) {
                $categories[$forum['categoryId']] = [
                    'id' => $forum['categoryId'],
                    'name' => $forum['categoryName'],
                    'order' => $forum['categoryOrder'],
                    'forums' => [],
                ];
            }

            $unreadDiscussions = 0;
            if ($this->userHelper->userIsLoggedIn()) {
                /**
                 * @var ForumForum $forumEntity
                 */
                $forumEntity = $this->doctrine->getRepository(ForumForum::class)->find($forum['id']);

                if ((int)$forum['type'] === ForumForum::TYPE_MODERATORS_ONLY
                    && !$this->forumAuthHelper->userIsModerator($forumEntity, $this->userHelper->getUser())
                ) {
                    // The user is not allowed to view this category
                    continue;
                }

                if ((int)$forum['type'] !== ForumForum::TYPE_ARCHIVE) {
                    $unreadDiscussions = $this->doctrine
                        ->getRepository(ForumForum::class)
                        ->getNumberOfUnreadPostsInForum($forumEntity, $this->userHelper->getUser());
                }
            } elseif ((int)$forum['type'] === ForumForum::TYPE_MODERATORS_ONLY) {
                // Guest is not allowed to view this category
                continue;
            }

            $categories[$forum['categoryId']]['forums'][] = [
                'id' => $forum['id'],
                'type' => $forum['type'],
                'name' => $forum['name'],
                'order' => $forum['order'],
                'numberOfDiscussions' => $forum['numberOfDiscussions'],
                'numberOfUnreadDiscussions' => $unreadDiscussions,
            ];
        }
        return $categories;
    }
}
