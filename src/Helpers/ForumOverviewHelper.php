<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\ForumForum;
use App\Repository\ForumForumRepository;
use App\Traits\SortTrait;
use Doctrine\Persistence\ManagerRegistry;

class ForumOverviewHelper
{
    use SortTrait;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly ForumAuthorizationHelper $forum_authorization_helper,
        private readonly UserHelper $user_helper,
    ) {
    }

    public function getCategoryArray(): array
    {
        /**
         * @var ForumForumRepository $forum_forum_repository
         */
        $forum_forum_repository = $this->doctrine->getRepository(ForumForum::class);

        $categories = [];
        $forums = $forum_forum_repository->findAllAndGetArray($this->user_helper->userIsLoggedIn() ? $this->user_helper->getUser()->id : null);
        foreach ($forums as $forum) {
            if (!isset($categories[$forum['categoryId']])) {
                $categories[$forum['categoryId']] = [
                    'id' => $forum['categoryId'],
                    'name' => $forum['categoryName'],
                    'order' => $forum['categoryOrder'],
                    'forums' => [],
                ];
            }

            $unread_discussions = 0;
            if ($this->user_helper->userIsLoggedIn()) {
                if ((int) $forum['type'] === ForumForum::TYPE_MODERATORS_ONLY
                    && (bool) $forum['user_is_moderator'] !== true
                ) {
                    // The user is not allowed to view this category
                    continue;
                }

                if ((int) $forum['type'] !== ForumForum::TYPE_ARCHIVE && (int) $forum['type'] !== ForumForum::TYPE_MODERATORS_ONLY) {
                    $unread_discussions = $forum_forum_repository->getNumberOfUnreadDiscussionsInForum((int) $forum['id'], $this->user_helper->getUser());
                }
            } elseif ((int) $forum['type'] === ForumForum::TYPE_MODERATORS_ONLY) {
                // Guest is not allowed to view this category
                continue;
            }

            $categories[$forum['categoryId']]['forums'][] = [
                'id' => $forum['id'],
                'type' => $forum['type'],
                'name' => $forum['name'],
                'description' => $forum['description'],
                'order' => $forum['order'],
                'number_of_discussions' => $forum['number_of_discussions'],
                'number_of_unread_discussions' => $unread_discussions,
            ];
        }
        return $categories;
    }
}
