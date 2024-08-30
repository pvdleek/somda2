<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Entity\ForumPost;
use App\Entity\UserPreference;
use App\Exception\WrongMethodError;
use App\Form\ForumPost as ForumPostForm;
use App\Generics\ForumGenerics;
use App\Repository\ForumDiscussion as RepositoryForumDiscussion;
use App\Repository\ForumPost as RepositoryForumPost;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ForumDiscussionHelper
{
    private ?ForumDiscussion $discussion = null;

    private ?int $numberOfPages = null;

    private ?int $numberOfPosts = null;

    private ?int $pageNumber = null;

    private ?string $forumJump = null;

    private ?int $numberOfReadPosts = null;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
        private readonly ForumAuthorizationHelper $forumAuthHelper,
        private readonly RepositoryForumDiscussion $forumDiscussionRepository,
        private readonly RepositoryForumPost $repositoryForumPost,
    ) {
    }

    public function setDiscussion(ForumDiscussion $discussion): ForumDiscussionHelper
    {
        $this->discussion = $discussion;

        if (!$this->forumAuthHelper->mayView($discussion->forum, $this->userHelper->getUser())) {
            throw new AccessDeniedException('The user may not view this discussion');
        }

        return $this;
    }

    /**
     * @return ForumPost[]
     * @throws WrongMethodError
     */
    public function getPosts(bool $newToOld, int $requestedPageNumber = null, int $requestedPostId = null): array
    {
        $this->setNumberOfPostsAndPages();
        $this->setNumberOfReadPosts();
        $this->setForumJump($requestedPageNumber, $requestedPostId);
        $this->setPageNumber($newToOld, $requestedPageNumber, $requestedPostId);

        $this->discussion->viewed = (int) $this->discussion->viewed + 1;
        $this->doctrine->getManager()->flush();

        /**
         * @var ForumPost[] $posts
         */
        $posts = $this->repositoryForumPost->findBy(
            [ForumPostForm::FIELD_DISCUSSION => $this->discussion],
            [ForumPostForm::FIELD_TIMESTAMP => $newToOld ? 'DESC' : 'ASC'],
            ForumGenerics::MAX_POSTS_PER_PAGE,
            ($this->pageNumber - 1) * ForumGenerics::MAX_POSTS_PER_PAGE
        );

        if ($this->userHelper->userIsLoggedIn()) {
            $this->forumDiscussionRepository->markPostsAsRead($this->userHelper->getUser(), $this->discussion, $posts);
        }

        return $posts;
    }

    /**
     * @return ForumPost[]
     * @throws \Exception
     */
    public function getNonPaginatedPosts(bool $newToOld): array
    {
        $this->setNumberOfPostsAndPages();
        $this->setNumberOfReadPosts();

        $this->discussion->viewed = (int) $this->discussion->viewed + 1;
        $this->doctrine->getManager()->flush();

        /**
         * @var ForumPost[] $posts
         */
        $posts = $this->repositoryForumPost->findBy(
            [ForumPostForm::FIELD_DISCUSSION => $this->discussion],
            [ForumPostForm::FIELD_TIMESTAMP => $newToOld ? 'DESC' : 'ASC']
        );

        if ($this->userHelper->userIsLoggedIn() ? (bool)$this->userHelper->getPreferenceByKey(UserPreference::KEY_APP_MARK_FORUM_READ)->value : false) {
            $this->forumDiscussionRepository->markPostsAsRead($this->userHelper->getUser(), $this->discussion, $posts);
        }

        return $posts;
    }

    /**
     * @throws WrongMethodError
     */
    public function getNumberOfPages(): int
    {
        if (null === $this->numberOfPages) {
            throw new WrongMethodError('Execute the getPosts() method first');
        }

        return $this->numberOfPages;
    }

    /**
     * @throws WrongMethodError
     */
    public function getNumberOfPosts(): int
    {
        if (null === $this->numberOfPosts) {
            throw new WrongMethodError('Execute the getPosts() method first');
        }

        return $this->numberOfPosts;
    }

    /**
     * @throws WrongMethodError
     */
    public function getPageNumber(): int
    {
        if (null === $this->pageNumber) {
            throw new WrongMethodError('Execute the getPosts() method first');
        }

        return $this->pageNumber;
    }

    public function getForumJump(): ?string
    {
        return $this->forumJump;
    }

    /**
     * @throws WrongMethodError
     */
    public function getNumberOfReadPosts(): int
    {
        if (null === $this->numberOfReadPosts) {
            throw new WrongMethodError('Execute the getPosts() method first');
        }
        return $this->numberOfReadPosts;
    }

    private function setNumberOfPostsAndPages(): void
    {
        $this->numberOfPosts = $this->forumDiscussionRepository->getNumberOfPosts($this->discussion);
        $this->numberOfPages = (int)floor(($this->numberOfPosts - 1) / ForumGenerics::MAX_POSTS_PER_PAGE) + 1;
    }

    /**
     * This function should always be called before setPageNumber for that function modifies the pageNumber
     */
    private function setForumJump(int $requestedPageNumber = null, int $requestedPostId = null): void
    {
        if (null !== $requestedPostId) {
            $this->forumJump = 'p' . $requestedPostId;
            return;
        }
        if (null === $requestedPageNumber
            && $this->discussion->forum->type !== ForumForum::TYPE_ARCHIVE
            && $this->userHelper->userIsLoggedIn()
        ) {
            $this->forumJump = 'new_post';
        }
    }

    /**
     * This function should always be called after setForumJump for this function modifies the pageNumber
     * @throws WrongMethodError
     */
    private function setPageNumber(bool $newToOld, int $requestedPageNumber = null, int $postId = null): void
    {
        if (null !== $requestedPageNumber) {
            $this->pageNumber = \max($requestedPageNumber, 1);
            return;
        }

        if (null !== $postId) {
            // A specific post was requested, so we go to this post
            $postNumber = $this->forumDiscussionRepository->getPostNumberInDiscussion($this->discussion, $postId);
            $this->pageNumber = (int)floor($postNumber / ForumGenerics::MAX_POSTS_PER_PAGE) + 1;
            return;
        }

        if ($this->discussion->forum->type !== ForumForum::TYPE_ARCHIVE && $this->userHelper->userIsLoggedIn()) {
            // Neither a specific page or post were requested but the user is logged in,
            // so we will go to the first unread post in the discussion
            $this->pageNumber = (int) \floor($this->getNumberOfReadPosts() / ForumGenerics::MAX_POSTS_PER_PAGE) + 1;
            if ($newToOld) {
                $this->pageNumber = \max($this->numberOfPages - $this->pageNumber, 1);
            }
            return;
        }

        $this->pageNumber = $newToOld ? $this->numberOfPages : 1;
    }

    private function setNumberOfReadPosts(): void
    {
        if ($this->userHelper->userIsLoggedIn()) {
            if ($this->discussion->forum->type === ForumForum::TYPE_ARCHIVE) {
                $this->numberOfReadPosts = 9999999;
                return;
            }
            $this->numberOfReadPosts = $this->forumDiscussionRepository->getNumberOfReadPosts($this->discussion, $this->userHelper->getUser());
            return;
        }

        $this->numberOfReadPosts = 0;
    }
}
