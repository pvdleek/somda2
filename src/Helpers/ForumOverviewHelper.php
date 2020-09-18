<?php
declare(strict_types=1);

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
        $forums = $this->doctrine->getRepository(ForumForum::class)->findAllAndGetArray(
            $this->userHelper->userIsLoggedIn() ? $this->userHelper->getUser()->id : null
        );
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
                if ((int)$forum['type'] === ForumForum::TYPE_MODERATORS_ONLY
                    && (bool)$forum['userIsModerator'] !== true
                ) {
                    // The user is not allowed to view this category
                    continue;
                }

                if ((int)$forum['type'] !== ForumForum::TYPE_ARCHIVE) {
                    $unreadDiscussions = $this->doctrine
                        ->getRepository(ForumForum::class)
                        ->getNumberOfUnreadPostsInForum((int)$forum['id'], $this->userHelper->getUser());
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
