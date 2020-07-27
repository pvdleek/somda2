<?php

namespace App\Controller\Api;

use App\Entity\ForumDiscussion;
use App\Entity\ForumPost;
use App\Generics\ForumGenerics;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\ForumDiscussionHelper;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
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
    private ForumDiscussionHelper $forumDiscussionHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     * @param ForumAuthorizationHelper $forumAuthHelper
     * @param ForumDiscussionHelper $forumDiscussionHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        UserHelper $userHelper,
        ForumAuthorizationHelper $forumAuthHelper,
        ForumDiscussionHelper $forumDiscussionHelper
    ) {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
        $this->forumAuthHelper = $forumAuthHelper;
        $this->forumDiscussionHelper = $forumDiscussionHelper;
    }

    /**
     * @IsGranted("ROLE_API_USER")
     * @param int $id
     * @param int|null $pageNumber
     * @param int|null $postId
     * @return Response
     * @throws Exception
     * @SWG\Parameter(
     *     default="",
     *     description="Fill this to request a specific page-number",
     *     in="path",
     *     name="pageNumber",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     default="",
     *     description="Fill this to request a specific post",
     *     in="path",
     *     name="postId",
     *     type="integer",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns paginated posts in a discussion",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=ForumPost::class))
     *     )
     * )
     * @SWG\Tag(name="Forum")
     */
    public function indexAction(int $id, int $pageNumber = null, int $postId = null): Response
    {
        /**
         * @var ForumDiscussion $discussion
         */
        $discussion = $this->doctrine->getRepository(ForumDiscussion::class)->find($id);
        if (is_null($discussion)) {
            throw new AccessDeniedException('This discussion does not exist');
        }

        $this->forumDiscussionHelper->setDiscussion($discussion);
        $posts = $this->forumDiscussionHelper->getPosts($pageNumber, $postId);

        return $this->handleView($this->view([
            'data' => $posts,
            'meta' => [
                'user_is_moderator' =>
                    $this->forumAuthHelper->userIsModerator($discussion->forum, $this->userHelper->getUser()),
                'posts_per_page' => ForumGenerics::MAX_POSTS_PER_PAGE,
                'number_of_pages' => $this->forumDiscussionHelper->getNumberOfPages(),
                'page_number' => $this->forumDiscussionHelper->getPageNumber(),
                'may_post' => $this->forumAuthHelper->mayPost($discussion->forum, $this->userHelper->getUser()),
                'number_of_read_posts' => $this->forumDiscussionHelper->getNumberOfReadPosts(),
                'forum_jump' => $this->forumDiscussionHelper->getForumJump(),
            ]
        ], 200));
    }

    /**
     * @IsGranted("ROLE_API_USER")
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="Returns the favorite discussions of the user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=ForumDiscussion::class))
     *     )
     * )
     * @SWG\Tag(name="Forum")
     */
    public function favoritesAction(): Response
    {
        if (!$this->userHelper->userIsLoggedIn()) {
            throw new AccessDeniedException('The user is not logged in');
        }

        $discussions = $this->doctrine->getRepository(ForumDiscussion::class)->findByFavorites(
            $this->userHelper->getUser()
        );
        return $this->handleView($this->view(['data' => $discussions], 200));
    }

    /**
     * @IsGranted("ROLE_API_USER")
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="Returns all unread discussions of the user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=ForumDiscussion::class))
     *     )
     * )
     * @SWG\Tag(name="Forum")
     */
    public function unreadAction(): Response
    {
        if (!$this->userHelper->userIsLoggedIn()) {
            throw new AccessDeniedException('The user is not logged in');
        }

        $discussions = $this->doctrine->getRepository(ForumDiscussion::class)->findUnread($this->userHelper->getUser());
        return $this->handleView($this->view(['data' => $discussions], 200));
    }
}
