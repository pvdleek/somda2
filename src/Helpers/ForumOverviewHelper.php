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
        private readonly UserHelper $user_helper,
    ) {
    }

    public function getCategoryArray(): array
    {
        /** @var ForumForumRepository $forum_forum_repository */
        $forum_forum_repository = $this->doctrine->getRepository(ForumForum::class);

        $categories = [];
        $user = $this->user_helper->userIsLoggedIn() ? $this->user_helper->getUser() : null;
        $forums = $forum_forum_repository->findAllAndGetArray($user?->id);

        $unread_counts_by_forum = [];
        if (null !== $user) {
            $eligible_forum_ids = [];
            foreach ($forums as $forum) {
                if ((int) $forum['type'] !== ForumForum::TYPE_ARCHIVE && (int) $forum['type'] !== ForumForum::TYPE_MODERATORS_ONLY) {
                    $eligible_forum_ids[] = (int) $forum['id'];
                }
            }
            if (!empty($eligible_forum_ids)) {
                $unread_counts_by_forum = $forum_forum_repository->getUnreadDiscussionCountsByForum($eligible_forum_ids, $user);
            }
        }

        foreach ($forums as $forum) {
            if (!isset($categories[$forum['categoryId']])) {
                $categories[$forum['categoryId']] = [
                    'id' => $forum['categoryId'],
                    'name' => $forum['categoryName'],
                    'order' => $forum['categoryOrder'],
                    'forums' => [],
                ];
            }

            if (null !== $user) {
                if ((int) $forum['type'] === ForumForum::TYPE_MODERATORS_ONLY
                    && (bool) $forum['user_is_moderator'] !== true
                ) {
                    continue;
                }
            } elseif ((int) $forum['type'] === ForumForum::TYPE_MODERATORS_ONLY) {
                continue;
            }

            $categories[$forum['categoryId']]['forums'][] = [
                'id' => $forum['id'],
                'type' => $forum['type'],
                'name' => $forum['name'],
                'description' => $forum['description'],
                'order' => $forum['order'],
                'number_of_discussions' => $forum['number_of_discussions'],
                'number_of_unread_discussions' => (int) ($unread_counts_by_forum[(int) $forum['id']] ?? 0),
            ];
        }
        return $categories;
    }
}
