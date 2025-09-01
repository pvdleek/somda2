<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\ForumForum;
use App\Generics\RoleGenerics;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\ForumOverviewHelper;
use App\Helpers\UserHelper;
use App\Repository\ForumDiscussionRepository;
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
        private readonly ForumAuthorizationHelper $forum_authorization_helper,
        private readonly ForumOverviewHelper $forum_overview_helper,
        private readonly UserHelper $user_helper,
        private readonly ForumDiscussionRepository $forum_discussion_repository,
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
     *                 property="number_of_discussions",
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
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        $categories = $this->sortByFieldFilter($this->forum_overview_helper->getCategoryArray(), 'order');

        $forums = [];
        foreach ($categories as $id => $category) {
            $category_forums = $this->sortByFieldFilter($category['forums'], 'order');
            foreach (\array_keys($category_forums) as $key) {
                $category_forums[$key]['category'] = [
                    'id' => $id,
                    'name' => $category['name'],
                    'order' => $category['order'],
                ];
            }
            $forums = \array_merge($forums, $category_forums);
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
     *                 description="Identifier of the last post in the discussion read by the user",
     *                 property="post_last_read",
     *                 type="integer",
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
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        /**
         * @var ForumForum $forum
         */
        $forum = $this->doctrine->getRepository(ForumForum::class)->find($id);
        if (null === $forum) {
            return $this->indexAction();
        }

        $discussions = $this->forum_discussion_repository->findByForum($forum, $this->user_helper->getUser(), 50);
        return $this->handleView($this->view([
            'meta' => ['user_is_moderator' =>
                $this->forum_authorization_helper->userIsModerator($forum, $this->user_helper->getUser()),
            ],
            'data' => $discussions,
        ], 200));
    }
}
