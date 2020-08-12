<?php

namespace App\Controller\Api;

use App\Entity\ForumCategory;
use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\ForumOverviewHelper;
use App\Helpers\UserHelper;
use App\Traits\SortTrait;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
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
     *     @SWG\Property(property="data", type="array", @SWG\Items(
     *         @SWG\Property(description="Unique identifier", property="id", type="integer"),
     *         @SWG\Property(
     *             description="The forum-type:\
     *                 0 for publicly accessible,
     *                 1 for logged-in users only,
     *                 3 for moderators only,
     *                 4 for archived forums (no recording of read/unread posts)",
     *             enum={0,1,3,4},
     *             property="type",
     *             type="integer",
     *         ),
     *         @SWG\Property(description="Name of the forum", maxLength=40, property="name", type="string"),
     *         @SWG\Property(
     *             description="The order in which to display the forums",
     *             property="order",
     *             type="integer",
     *         ),
     *         @SWG\Property(
     *             description="The total number of discussions in this forum",
     *             property="numberOfDiscussions",
     *             type="integer",
     *         ),
     *         @SWG\Property(
     *             description="The total number of unread discussions in this forum",
     *             property="numberOfUnreadDiscussions",
     *             type="integer",
     *         ),
     *         @SWG\Property(
     *             description="Unique identifier",
     *             property="category",
     *             ref=@Model(type=ForumCategory::class),
     *         ),
     *     )
     * )
     * @SWG\Tag(name="Forum")
     */
    public function indexAction(): Response
    {
        $categories = $this->sortByFieldFilter($this->forumOverviewHelper->getCategoryArray(), 'order');
        
        $forums = [];
        foreach ($categories as $id => $category) {
            $categoryForums = $this->sortByFieldFilter($category['forums'], 'order');
            foreach ($categoryForums as $key => $categoryForum) {
                $categoryForums[$key]['category'] = [
                    'id' => $id,
                    'name' => $category['name'],
                    'order' => $category['order'],
                ];
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
     *     @SWG\Property(
     *         property="meta",
     *         type="object",
     *         @SWG\Property(
     *             description="Whether the user is a moderator for this forum",
     *             property="user_is_moderator",
     *             type="boolean",
     *         ),
     *     ),
     *     @SWG\Property(property="data", type="array", @SWG\Items(
     *         @SWG\Property(description="Unique identifier", property="id", type="integer"),
     *         @SWG\Property(description="Title of the discussion", maxLength=50, property="title", type="string"),
     *         @SWG\Property(
     *             description="Unique identifier of the user that started the discussion",
     *              property="author_id",
     *            type="integer",
     *         ),
     *         @SWG\Property(
     *             description="Username of the user that started the discussion",
     *             maxLength=20,
     *             property="author_username",
     *             type="string",
     *         ),
     *         @SWG\Property(
     *             description="Whether the discussion is locked",
     *             enum={"0","1"},
     *             property="locked",
     *             type="string",
     *         ),
     *         @SWG\Property(
     *             description="The number of times the discussion has been viewed",
     *             property="viewed",
     *             type="integer",
     *         ),
     *         @SWG\Property(
     *             description="Whether the discussion is fully read by the user",
     *             enum={"0","1"},
     *             property="discussion_read",
     *             type="string",
     *         ),
     *         @SWG\Property(
     *             description="Timestamp of the last post in this discussion (Y-m-d H:i:s)",
     *             property="max_post_timestamp",
     *             type="string",
     *         ),
     *         @SWG\Property(
     *             description="The number of posts in this discussion",
     *             property="posts",
     *             type="integer",
     *         ),
     *     )
     * )
     * @SWG\Tag(name="Forum")
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
            'meta' => ['user_is_moderator' =>
                $this->forumAuthHelper->userIsModerator($forum, $this->userHelper->getUser()),
            ],
            'data' => $discussions,
        ], 200));
    }
}
