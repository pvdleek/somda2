<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Entity\ForumPost;
use App\Exception\WrongMethodError;
use App\Form\ForumPost as ForumPostForm;
use App\Generics\ForumGenerics;
use App\Repository\ForumDiscussionRepository;
use App\Repository\ForumPostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ForumDiscussionHelper
{
    private ?ForumDiscussion $discussion = null;

    private ?int $number_of_pages = null;

    private ?int $number_of_posts = null;

    private ?int $page_number = null;

    private ?string $forum_jump = null;

    private ?int $number_of_read_posts = null;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $user_helper,
        private readonly ForumAuthorizationHelper $forum_authorization_helper,
        private readonly ForumDiscussionRepository $forum_discussion_repository,
        private readonly ForumPostRepository $forum_post_repository,
    ) {
    }

    public function setDiscussion(ForumDiscussion $discussion): ForumDiscussionHelper
    {
        $this->discussion = $discussion;

        if (!$this->forum_authorization_helper->mayView($discussion->forum, $this->user_helper->getUser())) {
            throw new AccessDeniedException('The user may not view this discussion');
        }

        return $this;
    }

    /**
     * @return ForumPost[]
     * @throws WrongMethodError
     */
    public function getPosts(bool $new_to_old, ?int $requested_page_number = null, ?int $requested_post_id = null): array
    {
        $this->setNumberOfPostsAndPages();
        $this->setNumberOfReadPosts();
        $this->setForumJump($requested_page_number, $requested_post_id);
        $this->setPageNumber($new_to_old, $requested_page_number, $requested_post_id);

        $this->discussion->viewed = (int) $this->discussion->viewed + 1;
        $this->doctrine->getManager()->flush();

        /** @var ForumPost[] $posts */
        $posts = $this->forum_post_repository->findBy(
            [ForumPostForm::FIELD_DISCUSSION => $this->discussion],
            [ForumPostForm::FIELD_TIMESTAMP => $new_to_old ? 'DESC' : 'ASC'],
            ForumGenerics::MAX_POSTS_PER_PAGE,
            ($this->page_number - 1) * ForumGenerics::MAX_POSTS_PER_PAGE
        );

        if ($this->user_helper->userIsLoggedIn()) {
            $this->forum_discussion_repository->markPostsAsRead($this->user_helper->getUser(), $this->discussion, $posts);
        }

        return $posts;
    }

    /**
     * @return ForumPost[]
     * @throws \Exception
     */
    public function getNonPaginatedPosts(bool $new_to_old): array
    {
        $this->setNumberOfPostsAndPages();
        $this->setNumberOfReadPosts();

        $this->discussion->viewed = (int) $this->discussion->viewed + 1;
        $this->doctrine->getManager()->flush();

        /** @var ForumPost[] $posts */
        $posts = $this->forum_post_repository->findBy(
            [ForumPostForm::FIELD_DISCUSSION => $this->discussion],
            [ForumPostForm::FIELD_TIMESTAMP => $new_to_old ? 'DESC' : 'ASC']
        );

        return $posts;
    }

    /**
     * @throws WrongMethodError
     */
    public function getNumberOfPages(): int
    {
        if (null === $this->number_of_pages) {
            throw new WrongMethodError('Execute the getPosts() method first');
        }

        return $this->number_of_pages;
    }

    /**
     * @throws WrongMethodError
     */
    public function getNumberOfPosts(): int
    {
        if (null === $this->number_of_posts) {
            throw new WrongMethodError('Execute the getPosts() method first');
        }

        return $this->number_of_posts;
    }

    /**
     * @throws WrongMethodError
     */
    public function getPageNumber(): int
    {
        if (null === $this->page_number) {
            throw new WrongMethodError('Execute the getPosts() method first');
        }

        return $this->page_number;
    }

    public function getForumJump(): ?string
    {
        return $this->forum_jump;
    }

    /**
     * @throws WrongMethodError
     */
    public function getNumberOfReadPosts(): int
    {
        if (null === $this->number_of_read_posts) {
            throw new WrongMethodError('Execute the getPosts() method first');
        }
        return $this->number_of_read_posts;
    }

    private function setNumberOfPostsAndPages(): void
    {
        $this->number_of_posts = $this->forum_discussion_repository->getNumberOfPosts($this->discussion);
        $this->number_of_pages = (int) \floor(($this->number_of_posts - 1) / ForumGenerics::MAX_POSTS_PER_PAGE) + 1;
    }

    /**
     * This function should always be called before setPageNumber for that function modifies the page_number
     */
    private function setForumJump(?int $requested_page_number = null, ?int $requested_post_id = null): void
    {
        if (null !== $requested_post_id) {
            $this->forum_jump = 'p'.$requested_post_id;
            return;
        }
        if (null === $requested_page_number
            && $this->discussion->forum->type !== ForumForum::TYPE_MODERATORS_ONLY
            && $this->discussion->forum->type !== ForumForum::TYPE_ARCHIVE
            && $this->user_helper->userIsLoggedIn()
        ) {
            $this->forum_jump = 'new_post';
        }
    }

    /**
     * This function should always be called after setForumJump for this function modifies the page_number
     * @throws WrongMethodError
     */
    private function setPageNumber(bool $new_to_old, ?int $requested_page_number = null, ?int $post_id = null): void
    {
        if (null !== $requested_page_number) {
            $this->page_number = \max($requested_page_number, 1);
            return;
        }

        if (null !== $post_id) {
            // A specific post was requested, so we go to this post
            $post_number = $this->forum_discussion_repository->getPostNumberInDiscussion($this->discussion, $post_id);
            $this->page_number = (int) \floor($post_number / ForumGenerics::MAX_POSTS_PER_PAGE) + 1;
            return;
        }

        if ($this->discussion->forum->type !== ForumForum::TYPE_MODERATORS_ONLY
            && $this->discussion->forum->type !== ForumForum::TYPE_ARCHIVE
            && $this->user_helper->userIsLoggedIn()
        ) {
            // Neither a specific page or post were requested but the user is logged in,
            // so we will go to the first unread post in the discussion
            if ($this->number_of_read_posts === $this->number_of_posts) {
                $this->page_number = (int) \floor(($this->getNumberOfReadPosts() - 1) / ForumGenerics::MAX_POSTS_PER_PAGE) + 1;
                return;
            }

            $this->page_number = (int) \floor($this->getNumberOfReadPosts() / ForumGenerics::MAX_POSTS_PER_PAGE) + 1;
            if ($new_to_old) {
                $this->page_number = \max($this->number_of_pages - $this->page_number, 1);
            }
            return;
        }

        $this->page_number = $new_to_old ? $this->number_of_pages : 1;
    }

    private function setNumberOfReadPosts(): void
    {
        if ($this->user_helper->userIsLoggedIn()) {
            if ($this->discussion->forum->type === ForumForum::TYPE_MODERATORS_ONLY || $this->discussion->forum->type === ForumForum::TYPE_ARCHIVE) {
                $this->number_of_read_posts = 9999999;
                return;
            }
            $this->number_of_read_posts = $this->forum_discussion_repository->getNumberOfReadPosts($this->discussion, $this->user_helper->getUser());
            return;
        }

        $this->number_of_read_posts = 0;
    }
}
