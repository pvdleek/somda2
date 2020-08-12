<?php

namespace App\Helpers;

use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Entity\ForumPost;
use App\Exception\WrongMethodError;
use App\Form\ForumPost as ForumPostForm;
use App\Generics\ForumGenerics;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ForumDiscussionHelper
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
     * @var ForumDiscussion|null
     */
    private ?ForumDiscussion $discussion;

    /**
     * @var int|null
     */
    private ?int $numberOfPages;

    /**
     * @var int|null
     */
    private ?int $pageNumber;

    /**
     * @var string|null
     */
    private ?string $forumJump = null;

    /**
     * @var int|null
     */
    private ?int $numberOfReadPosts;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     * @param ForumAuthorizationHelper $forumAuthHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        UserHelper $userHelper,
        ForumAuthorizationHelper $forumAuthHelper
    ) {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
        $this->forumAuthHelper = $forumAuthHelper;
    }

    /**
     * @param ForumDiscussion $discussion
     * @return ForumDiscussionHelper
     */
    public function setDiscussion(ForumDiscussion $discussion): ForumDiscussionHelper
    {
        $this->discussion = $discussion;

        if (!$this->forumAuthHelper->mayView($discussion->forum, $this->userHelper->getUser())) {
            throw new AccessDeniedException('The user may not view this discussion');
        }

        return $this;
    }

    /**
     * @param int|null $requestedPageNumber
     * @param int|null $requestedPostId
     * @return ForumPost[]
     * @throws WrongMethodError
     */
    public function getPosts(int $requestedPageNumber = null, int $requestedPostId = null): array
    {
        $this->setNumberOfPages();
        $this->setNumberOfReadPosts();
        $this->setForumJump($requestedPageNumber, $requestedPostId);
        $this->setPageNumber($requestedPageNumber, $requestedPostId);

        $this->discussion->viewed = (int)$this->discussion->viewed + 1;
        $this->doctrine->getManager()->flush();

        /**
         * @var ForumPost[] $posts
         */
        $posts = $this->doctrine->getRepository(ForumPost::class)->findBy(
            [ForumPostForm::FIELD_DISCUSSION => $this->discussion],
            [ForumPostForm::FIELD_TIMESTAMP => 'ASC'],
            ForumGenerics::MAX_POSTS_PER_PAGE,
            ($this->pageNumber - 1) * ForumGenerics::MAX_POSTS_PER_PAGE
        );
//        if ($this->userHelper->userIsLoggedIn()) {
//            $this->doctrine->getRepository(ForumDiscussion::class)->markPostsAsRead(
//                $this->userHelper->getUser(),
//                $posts
//            );
//        }

        return $posts;
    }

    /**
     * @return int
     * @throws WrongMethodError
     */
    public function getNumberOfPages(): int
    {
        if (is_null($this->numberOfPages)) {
            throw new WrongMethodError('Execute the getPosts() method first');
        }

        return $this->numberOfPages;
    }

    /**
     * @return int
     * @throws WrongMethodError
     */
    public function getPageNumber(): int
    {
        if (is_null($this->pageNumber)) {
            throw new WrongMethodError('Execute the getPosts() method first');
        }

        return $this->pageNumber;
    }

    /**
     * @return string|null
     */
    public function getForumJump(): ?string
    {
        return $this->forumJump;
    }

    /**
     * @return int
     * @throws WrongMethodError
     */
    public function getNumberOfReadPosts(): int
    {
        if (is_null($this->numberOfReadPosts)) {
            throw new WrongMethodError('Execute the getPosts() method first');
        }
        return $this->numberOfReadPosts;
    }

    /**
     *
     */
    private function setNumberOfPages(): void
    {
        $numberOfPosts = $this->doctrine->getRepository(ForumDiscussion::class)->getNumberOfPosts(
            $this->discussion
        );
        $this->numberOfPages = floor(($numberOfPosts - 1) / ForumGenerics::MAX_POSTS_PER_PAGE) + 1;
    }

    /**
     * This function should always be called before setPageNumber for that function modifies the pageNumber
     * @param int|null $requestedPageNumber
     * @param int|null $requestedPostId
     */
    private function setForumJump(int $requestedPageNumber = null, int $requestedPostId = null): void
    {
        if (!is_null($requestedPostId)) {
            $this->forumJump = 'p' . $requestedPostId;
            return;
        }
        if (is_null($requestedPageNumber)
            && $this->discussion->forum->type !== ForumForum::TYPE_ARCHIVE
            && $this->userHelper->userIsLoggedIn()
        ) {
            $this->forumJump = 'new_post';
        }
    }

    /**
     * This function should always be called after setForumJump for this function modifies the pageNumber
     * @param int|null $requestedPageNumber
     * @param int|null $postId
     * @throws WrongMethodError
     */
    private function setPageNumber(int $requestedPageNumber = null, int $postId = null): void
    {
        if (!is_null($requestedPageNumber)) {
            $this->pageNumber = $requestedPageNumber;
            return;
        }

        if (!is_null($postId)) {
            // A specific post was requested, so we go to this post
            $postNumber = $this->doctrine
                ->getRepository(ForumDiscussion::class)
                ->getPostNumberInDiscussion($this->discussion, $postId);
            $this->pageNumber = floor($postNumber / ForumGenerics::MAX_POSTS_PER_PAGE) + 1;
            return;
        }

        if ($this->discussion->forum->type !== ForumForum::TYPE_ARCHIVE && $this->userHelper->userIsLoggedIn()) {
            // Neither a specific page or post were requested but the user is logged in,
            // so we will go to the first unread post in the discussion
            $this->pageNumber =
                floor($this->getNumberOfReadPosts() / ForumGenerics::MAX_POSTS_PER_PAGE)
                + 1;
            return;
        }

        $this->pageNumber = 1;
    }

    /**
     *
     */
    private function setNumberOfReadPosts(): void
    {
        if ($this->userHelper->userIsLoggedIn()) {
            if ($this->discussion->forum->type === ForumForum::TYPE_ARCHIVE) {
                $this->numberOfReadPosts = 9999999;
                return;
            }
            $this->numberOfReadPosts = $this->doctrine
                ->getRepository(ForumDiscussion::class)
                ->getNumberOfReadPosts($this->discussion, $this->userHelper->getUser());
            return;
        }

        $this->numberOfReadPosts = 0;
    }
}
