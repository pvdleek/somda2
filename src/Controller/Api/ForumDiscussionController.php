<?php

namespace App\Controller\Api;

use App\Entity\ForumDiscussion;
use App\Entity\ForumPost;
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
     * @SWG\Tag(name="forum")
     */
    public function indexAction(int $id, int $pageNumber = null, int $postId = null): Response
    {
        /**
         * @var ForumDiscussion $discussion
         */
        $discussion = $this->doctrine->getRepository(ForumDiscussion::class)->find($id);
        if (is_null($discussion)) {
            throw new AccessDeniedException();
        }

        $this->forumDiscussionHelper->setDiscussion($discussion);
        $posts = $this->forumDiscussionHelper->getPosts($pageNumber, $postId);

        return $this->handleView($this->view([
            'data' => $posts,
            'meta' => [
                'user_is_moderator' =>
                    $this->forumAuthHelper->userIsModerator($discussion, $this->userHelper->getUser()),
                'number_of_pages' => $this->forumDiscussionHelper->getNumberOfPages(),
                'page_number' => $this->forumDiscussionHelper->getPageNumber(),
                'may_post' => $this->forumAuthHelper->mayPost($discussion->forum, $this->userHelper->getUser()),
                'number_of_read_posts' => $this->forumDiscussionHelper->getNumberOfReadPosts(),
                'forum_jump' => $this->forumDiscussionHelper->getForumJump(),
            ]
        ], 200));
    }
}
