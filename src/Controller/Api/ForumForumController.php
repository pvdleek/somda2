<?php

namespace App\Controller\Api;

use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\ForumOverviewHelper;
use App\Helpers\UserHelper;
use App\Traits\SortTrait;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

class ForumForumController extends AbstractFOSRestController
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
     * @var ForumOverviewHelper
     */
    private ForumOverviewHelper $forumOverviewHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     * @param ForumAuthorizationHelper $forumAuthHelper
     * @param ForumOverviewHelper $forumOverviewHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        UserHelper $userHelper,
        ForumAuthorizationHelper $forumAuthHelper,
        ForumOverviewHelper $forumOverviewHelper
    ) {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
        $this->forumAuthHelper = $forumAuthHelper;
        $this->forumOverviewHelper = $forumOverviewHelper;
    }

    /**
     * @IsGranted("ROLE_API_USER")
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="Returns all categories and forums",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items()
     *     )
     * )
     * @SWG\Tag(name="forum")
     */
    public function indexAction(): Response
    {
        $categories = $this->sortByFieldFilter($this->forumOverviewHelper->getCategoryArray(), 'order');
        
        $forums = [];
        foreach ($categories as $id => $category) {
            $categoryForums = $this->sortByFieldFilter($category['forums'], 'order');
            foreach ($categoryForums as $key => $categoryForum) {
                $categoryForums[$key]['category'] = ['id' => $id, 'name' => $category['name']];
            }
            $forums = array_merge($forums, $categoryForums);
        }

        return $this->handleView($this->view(['data' => $forums], 200));
    }

    /**
     * @IsGranted("ROLE_API_USER")
     * @param int $id
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="Returns all discussions in a forum",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items()
     *     )
     * )
     * @SWG\Tag(name="forum")
     */
    public function forumAction(int $id): Response
    {
        /**
         * @var ForumForum $forum
         */
        $forum = $this->doctrine->getRepository(ForumForum::class)->find($id);
        if (is_null($forum)) {
            return $this->indexAction();
        }

        $discussions = $this->doctrine
            ->getRepository(ForumDiscussion::class)
            ->findByForum($forum, $this->userHelper->getUser(), 50);
        return $this->handleView($this->view([
            'data' => $discussions,
            'meta' => ['user_is_moderator' =>
                $this->forumAuthHelper->userIsModerator($discussions[0], $this->userHelper->getUser()),
            ],
        ], 200));
    }
}
