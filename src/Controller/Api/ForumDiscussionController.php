<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\ForumDiscussion;
use App\Entity\UserPreference;
use App\Generics\RoleGenerics;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\ForumDiscussionHelper;
use App\Helpers\UserHelper;
use App\Repository\ForumDiscussionRepository;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ForumDiscussionController extends AbstractFOSRestController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly ForumAuthorizationHelper $forum_authorization_helper,
        private readonly ForumDiscussionHelper $discussion_helper,
        private readonly UserHelper $user_helper,
        private readonly ForumDiscussionRepository $forum_discussion_repository,
    ) {
    }

    /**
     * @throws \Exception
     * @OA\Response(
     *     response=200,
     *     description="Returns non-paginated posts in a discussion",
     *     @OA\Schema(
     *         @OA\Property(
     *             property="meta",
     *             type="object",
     *             @OA\Property(
     *                 description="Whether the user is a moderator for this forum",
     *                 property="user_is_moderator",
     *                 type="boolean",
     *             ),
     *             @OA\Property(
     *                 description="The total number of posts in this discussion",
     *                 property="number_of_posts",
     *                 type="integer",
     *             ),
     *             @OA\Property(
     *                 description="Whether the posts are displayed from new to old for this user",
     *                 property="new_to_old",
     *                 type="boolean",
     *             ),
     *             @OA\Property(
     *                 description="Whether the user is allowed to post a reply in this discussion",
     *                 property="may_post",
     *                 type="boolean",
     *             ),
     *             @OA\Property(
     *                 description="The total number of posts the user has already read in this discussion",
     *                 property="number_of_read_posts",
     *                 type="integer",
     *             ),
     *         ),
     *         @OA\Property(property="data", type="array", @OA\Items(ref=@Model(type=ForumPost::class))),
     *     ),
     * )
     * @OA\Tag(name="Forum")
     */
    public function indexAction(int $id): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        /**
         * @var ForumDiscussion $discussion
         */
        $discussion = $this->doctrine->getRepository(ForumDiscussion::class)->find($id);
        if (null === $discussion) {
            throw new AccessDeniedException('This discussion does not exist');
        }

        $new_to_old = $this->user_helper->userIsLoggedIn() ?
            (bool) $this->user_helper->getPreferenceByKey(UserPreference::KEY_FORUM_NEW_TO_OLD)->value : false;

        $this->discussion_helper->setDiscussion($discussion);
        $posts = $this->discussion_helper->getNonPaginatedPosts($new_to_old);

        return $this->handleView($this->view([
            'meta' => [
                'user_is_moderator' =>
                    $this->forum_authorization_helper->userIsModerator($discussion->forum, $this->user_helper->getUser()),
                'number_of_posts' => $this->discussion_helper->getNumberOfPosts(),
                'new_to_old' => $new_to_old,
                'may_post' => $this->forum_authorization_helper->mayPost($discussion->forum, $this->user_helper->getUser()),
                'number_of_read_posts' => $this->discussion_helper->getNumberOfReadPosts(),
            ],
            'data' => $posts,
        ], 200));
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns the favorite discussions of the user",
     *     @OA\Schema(
     *         @OA\Property(property="data", type="array", @OA\Items(ref=@Model(type=ForumDiscussion::class))),
     *     ),
     * )
     * @OA\Tag(name="Forum")
     */
    public function favoritesAction(): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if (!$this->user_helper->userIsLoggedIn()) {
            throw new AccessDeniedException('The user is not logged in');
        }

        $discussions = $this->forum_discussion_repository->findByFavorites($this->user_helper->getUser());

        return $this->handleView($this->view(['data' => $discussions], 200));
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns all unread discussions of the user",
     *     @OA\Schema(
     *         @OA\Property(property="data", type="array", @OA\Items(ref=@Model(type=ForumDiscussion::class))),
     *     ),
     * )
     * @OA\Tag(name="Forum")
     */
    public function unreadAction(): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if (!$this->user_helper->userIsLoggedIn()) {
            throw new AccessDeniedException('The user is not logged in');
        }

        $discussions = $this->forum_discussion_repository->findUnread($this->user_helper->getUser());

        return $this->handleView($this->view(['data' => $discussions], 200));
    }
}
