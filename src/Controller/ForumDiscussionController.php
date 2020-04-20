<?php

namespace App\Controller;

use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Entity\ForumPost;
use App\Form\ForumDiscussion as ForumDiscussionForm;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ForumDiscussionController extends ForumBaseController
{
    /**
     * @param Request $request
     * @param int $id
     * @param int|null $pageNumber
     * @param int|null $postId
     * @return Response|RedirectResponse
     */
    public function indexAction(Request $request, int $id, int $pageNumber = null, int $postId = null): Response
    {
        /**
         * @var ForumDiscussion $discussion
         */
        $discussion = $this->doctrine->getRepository(ForumDiscussion::class)->find($id);
        if (is_null($discussion)) {
            return $this->redirectToRoute('forum');
        }
        if (!$this->mayView($discussion->forum)) {
            throw new AccessDeniedHttpException();
        }

        $this->breadcrumbHelper->addPart('general.navigation.forum.index', 'forum');
        $this->breadcrumbHelper->addPart(
            $discussion->forum->category->name . ' == ' . $discussion->forum->name,
            'forum_forum',
            ['id' => $discussion->forum->getId(), 'name' => $discussion->forum->name]
        );
        $this->breadcrumbHelper->addPart(
            'Discussie',
            'forum_discussion',
            ['id' => $id, 'name' => $discussion->title],
            true
        );

        $discussion->viewed = $discussion->viewed + 1;
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
            if ($discussion->forum->type !== ForumForum::TYPE_ARCHIVE) {
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
            'userIsModerator' => $this->userIsModerator($discussion),
            'discussion' => $discussion,
            'numberOfPages' => $numberOfPages,
            'pageNumber' => $pageNumber,
            'posts' => $posts,
            'mayPost' => $this->mayPost($discussion->forum),
            'numberOfReadPosts' => $numberOfReadPosts,
            'forumBanner' => $this->getForumBanner($request),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function newAction(Request $request, int $id)
    {
        /**
         * @var ForumForum $forum
         */
        $forum = $this->doctrine->getRepository(ForumForum::class)->find($id);
        if (is_null($forum)) {
            return $this->redirectToRoute('forum');
        }

        $forumDiscussion = new ForumDiscussion();
        $forumDiscussion->forum = $forum;
        $forumDiscussion->author = $this->getUser();

        $form = $this->formFactory->create(ForumDiscussionForm::class, $forumDiscussion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addPost($form, $forumDiscussion);
            $this->doctrine->getManager()->persist($forumDiscussion);
            $this->doctrine->getManager()->flush();

            return $this->redirectToRoute(
                'forum_discussion',
                ['id' => $forumDiscussion->getId(), 'name' => urlencode($forumDiscussion->title)]
            );
        }

        return $this->render('forum/newDiscussion.html.twig', ['form' => $form->createView(), 'forum' => $forum]);
    }
}
