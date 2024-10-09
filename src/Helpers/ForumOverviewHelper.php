<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\ForumForum;
use App\Repository\ForumForum as RepositoryForumForum;
use App\Traits\SortTrait;
use Doctrine\Persistence\ManagerRegistry;

class ForumOverviewHelper
{
    use SortTrait;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
        private readonly ForumAuthorizationHelper $forumAuthHelper,
    ) {
    }

    public function getCategoryArray(): array
    {
        /**
         * @var RepositoryForumForum $forumForumRepository
         */
        $forumForumRepository = $this->doctrine->getRepository(ForumForum::class);

        $categories = [];
        $forums = $forumForumRepository->findAllAndGetArray($this->userHelper->userIsLoggedIn() ? $this->userHelper->getUser()->id : null);
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
                if ((int) $forum['type'] === ForumForum::TYPE_MODERATORS_ONLY
                    && (bool) $forum['userIsModerator'] !== true
                ) {
                    // The user is not allowed to view this category
                    continue;
                }

                if ((int) $forum['type'] !== ForumForum::TYPE_ARCHIVE && (int) $forum['type'] !== ForumForum::TYPE_MODERATORS_ONLY) {
                    $unreadDiscussions = $this->doctrine
                        ->getRepository(ForumForum::class)
                        ->getNumberOfUnreadDiscussionsInForum((int) $forum['id'], $this->userHelper->getUser());
                }
            } elseif ((int) $forum['type'] === ForumForum::TYPE_MODERATORS_ONLY) {
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
