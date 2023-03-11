<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\ForumDiscussion;
use App\Entity\UserPreference;
use App\Generics\RoleGenerics;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\ForumDiscussionHelper;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ForumDiscussionController extends AbstractFOSRestController
{
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
     * @var ForumDiscussionHelper
     */
    private ForumDiscussionHelper $discussionHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     * @param ForumAuthorizationHelper $forumAuthHelper
     * @param ForumDiscussionHelper $discussionHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        UserHelper $userHelper,
        ForumAuthorizationHelper $forumAuthHelper,
        ForumDiscussionHelper $discussionHelper
    ) {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
        $this->forumAuthHelper = $forumAuthHelper;
        $this->discussionHelper = $discussionHelper;
    }

    /**
     * @param int $id
     * @return Response
     * @throws Exception
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
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        /**
         * @var ForumDiscussion $discussion
         */
        $discussion = $this->doctrine->getRepository(ForumDiscussion::class)->find($id);
        if (is_null($discussion)) {
            throw new AccessDeniedException('This discussion does not exist');
        }

        $newToOld = $this->userHelper->userIsLoggedIn() ?
            (bool)$this->userHelper->getPreferenceByKey(UserPreference::KEY_FORUM_NEW_TO_OLD)->value : false;

        $this->discussionHelper->setDiscussion($discussion);
        $posts = $this->discussionHelper->getNonPaginatedPosts($newToOld);

        return $this->handleView($this->view([
            'meta' => [
                'user_is_moderator' =>
                    $this->forumAuthHelper->userIsModerator($discussion->forum, $this->userHelper->getUser()),
                'number_of_posts' => $this->discussionHelper->getNumberOfPosts(),
                'new_to_old' => $newToOld,
                'may_post' => $this->forumAuthHelper->mayPost($discussion->forum, $this->userHelper->getUser()),
                'number_of_read_posts' => $this->discussionHelper->getNumberOfReadPosts(),
            ],
            'data' => $posts,
        ], 200));
    }

    /**
     * @return Response
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
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if (!$this->userHelper->userIsLoggedIn()) {
            throw new AccessDeniedException('The user is not logged in');
        }

        $discussions = $this->doctrine->getRepository(ForumDiscussion::class)->findByFavorites(
            $this->userHelper->getUser()
        );
        return $this->handleView($this->view(['data' => $discussions], 200));
    }

    /**
     * @return Response
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
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if (!$this->userHelper->userIsLoggedIn()) {
            throw new AccessDeniedException('The user is not logged in');
        }

        $discussions = $this->doctrine->getRepository(ForumDiscussion::class)->findUnread($this->userHelper->getUser());
        return $this->handleView($this->view(['data' => $discussions], 200));
    }
}
