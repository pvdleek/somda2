<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\ForumCategory;
use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Generics\RoleGenerics;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\ForumOverviewHelper;
use App\Helpers\UserHelper;
use App\Traits\SortTrait;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class ForumForumController extends AbstractFOSRestController
{
    use SortTrait;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
        private readonly ForumAuthorizationHelper $forumAuthHelper,
        private readonly ForumOverviewHelper $forumOverviewHelper,
    ) {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns all categories and forums",
     *     @OA\Schema(
     *         @OA\Property(property="data", type="array", @OA\Items(
     *             @OA\Property(description="Unique identifier", property="id", type="integer"),
     *             @OA\Property(
     *                 description="The forum-type:\
     *                     0 for publicly accessible,
     *                     1 for logged-in users only,
     *                     3 for moderators only,
     *                     4 for archived forums (no recording of read/unread posts)",
     *                 enum={0,1,3,4},
     *                 property="type",
     *                 type="integer",
     *             ),
     *             @OA\Property(description="Name of the forum", maxLength=40, property="name", type="string"),
     *             @OA\Property(
     *                 description="The order in which to display the forums",
     *                 property="order",
     *                 type="integer",
     *             ),
     *             @OA\Property(
     *                 description="The total number of discussions in this forum",
     *                 property="numberOfDiscussions",
     *                 type="integer",
     *             ),
     *             @OA\Property(
     *                 description="Unique identifier",
     *                 property="category",
     *                 ref=@Model(type=ForumCategory::class),
     *             ),
     *         )),
     *     ),
     * )
     * @OA\Tag(name="Forum")
     */
    public function indexAction(): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        $categories = $this->sortByFieldFilter($this->forumOverviewHelper->getCategoryArray(), 'order');
        
        $forums = [];
        foreach ($categories as $id => $category) {
            $categoryForums = $this->sortByFieldFilter($category['forums'], 'order');
            foreach (\array_keys($categoryForums) as $key) {
                $categoryForums[$key]['category'] = [
                    'id' => $id,
                    'name' => $category['name'],
                    'order' => $category['order'],
                ];
            }
            $forums = \array_merge($forums, $categoryForums);
        }

        return $this->handleView($this->view(['data' => $forums], 200));
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns all discussions in a forum",
     *     @OA\Schema(
     *         @OA\Property(
     *             property="meta",
     *             type="object",
     *             @OA\Property(
     *                 description="Whether the user is a moderator for this forum",
     *                 property="user_is_moderator",
     *                 type="boolean",
     *             ),
     *         ),
     *         @OA\Property(property="data", type="array", @OA\Items(
     *             @OA\Property(description="Unique identifier", property="id", type="integer"),
     *             @OA\Property(description="Title of the discussion", maxLength=50, property="title", type="string"),
     *             @OA\Property(
     *                 description="Unique identifier of the user that started the discussion",
     *                  property="author_id",
     *                type="integer",
     *             ),
     *             @OA\Property(
     *                 description="Username of the user that started the discussion",
     *                 maxLength=20,
     *                 property="author_username",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 description="Whether the discussion is locked",
     *                 enum={"0","1"},
     *                 property="locked",
     *                 type="string",
     *             ),
     *            @OA\Property(
     *                 description="The number of times the discussion has been viewed",
     *                 property="viewed",
     *                 type="integer",
     *             ),
     *             @OA\Property(
     *                 description="Whether the discussion is fully read by the user",
     *                 enum={"0","1"},
     *                 property="discussion_read",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 description="Timestamp of the last post in this discussion (Y-m-d H:i:s)",
     *                 property="max_post_timestamp",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 description="The number of posts in this discussion",
     *                 property="posts",
     *                 type="integer",
     *             ),
     *         )),
     *     ),
     * )
     * @OA\Tag(name="Forum")
     */
    public function forumAction(int $id): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        /**
         * @var ForumForum $forum
         */
        $forum = $this->doctrine->getRepository(ForumForum::class)->find($id);
        if (\is_null($forum)) {
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
