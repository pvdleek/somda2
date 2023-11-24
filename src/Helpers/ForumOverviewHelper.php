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

            $categories[$forum['categoryId']]['forums'][] = [
                'id' => $forum['id'],
                'type' => $forum['type'],
                'name' => $forum['name'],
                'order' => $forum['order'],
                'numberOfDiscussions' => $forum['numberOfDiscussions'],
            ];
        }
        return $categories;
    }
}
