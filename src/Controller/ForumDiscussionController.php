<?php

namespace App\Controller;

use App\Entity\ForumDiscussion;
use App\Entity\ForumPost;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ForumDiscussionController extends ForumBaseController
{
    /**
     * @param Request $request
     * @param int $id
     * @param int|null $pageNumber
     * @param int|null $postId
     * @return Response|RedirectResponse
     */
    public function discussionAction(Request $request, int $id, int $pageNumber = null, int $postId = null): Response
    {
        /**
         * @var ForumDiscussion $discussion
         */
        $discussion = $this->doctrine->getRepository(ForumDiscussion::class)->find($id);
        if (is_null($discussion)) {
            return $this->redirectToRoute('forum');
        }
        if (!$this->mayView($discussion->getForum())) {
            throw new AccessDeniedException();
        }

        $this->breadcrumbHelper->addPart('general.navigation.forum.index', 'forum');
        $this->breadcrumbHelper->addPart(
            $discussion->getForum()->getCategory()->getName() . ' == ' . $discussion->getForum()->getName(),
            'forum_forum',
            ['id' => $discussion->getForum()->getId(), 'name' => $discussion->getForum()->getName()]
        );
        $this->breadcrumbHelper->addPart(
            'Discussie',
            'forum_discussion',
            ['id' => $id, 'name' => $discussion->getTitle()],
            true
        );

        $discussion->setViewed($discussion->getViewed() + 1);
        $this->doctrine->getManager()->flush();

        $numberOfPosts = $this->doctrine->getRepository(ForumDiscussion::class)->getNumberOfPosts($discussion);
        $numberOfPages = floor(($numberOfPosts - 1) / self::MAX_POSTS_PER_PAGE) + 1;
        if (is_null($pageNumber)) {
            $pageNumber = 1;
        }


//TODO postId afhandelen!!

        /**
         * @var ForumPost[] $posts
         */
        $numberOfReadPosts = 0;
        if ($this->userIsLoggedIn()) {
            if ($discussion->getForum()->getType() < 4) {
                $numberOfReadPosts = $this->doctrine->getRepository(ForumDiscussion::class)->getNumberOfReadPosts(
                    $discussion,
                    $this->getUser()
                );
                $pageNumber = floor($numberOfReadPosts / self::MAX_POSTS_PER_PAGE) + 1;
            }
            $posts = $this->doctrine->getRepository(ForumPost::class)->findBy(
                ['discussion' => $discussion],
                ['timestamp' => 'ASC'],
                self::MAX_POSTS_PER_PAGE,
                ($pageNumber - 1) * self::MAX_POSTS_PER_PAGE
            );
            $this->doctrine->getRepository(ForumDiscussion::class)->markPostsAsRead($this->getUser(), $posts);
        } else {
            $posts = $this->doctrine->getRepository(ForumPost::class)->findBy(
                ['discussion' => $discussion],
                ['timestamp' => 'ASC'],
                self::MAX_POSTS_PER_PAGE,
                ($pageNumber - 1) * self::MAX_POSTS_PER_PAGE
            );
        }

        return $this->render('forum/discussion.html.twig', [
            'userIsModerator' => in_array($this->getUser(), $discussion->getForum()->getModerators()),
            'discussion' => $discussion,
            'numberOfPages' => $numberOfPages,
            'pageNumber' => $pageNumber,
            'posts' => $posts,
            'mayPost' => $this->mayPost($discussion->getForum()),
            'numberOfReadPosts' => $numberOfReadPosts,
            'forumBanner' => $this->getForumBanner($request),
        ]);
    }
}
