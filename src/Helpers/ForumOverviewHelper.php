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
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     */
    public function __construct(ManagerRegistry $doctrine, UserHelper $userHelper)
    {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
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
                ];
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
