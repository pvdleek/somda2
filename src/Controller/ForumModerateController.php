<?php

namespace App\Controller;

use App\Entity\ForumDiscussion;
use App\Entity\ForumPost;
use App\Form\ForumDiscussionCombine;
use App\Form\ForumDiscussionMove;
use App\Generics\RouteGenerics;
use App\Helpers\FormHelper;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\RedirectHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ForumModerateController
{
    public const ACTION_CLOSE = 'close';
    public const ACTION_OPEN = 'open';
    public const ACTION_MOVE = 'move';

    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var RedirectHelper
     */
    private RedirectHelper $redirectHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @var ForumAuthorizationHelper
     */
    private ForumAuthorizationHelper $forumAuthHelper;

    /**
     * @param FormHelper $formHelper
     * @param UserHelper $userHelper
     * @param RedirectHelper $redirectHelper
     * @param TemplateHelper $templateHelper
     * @param ForumAuthorizationHelper $forumAuthHelper
     */
    public function __construct(
        FormHelper $formHelper,
        UserHelper $userHelper,
        RedirectHelper $redirectHelper,
        TemplateHelper $templateHelper,
        ForumAuthorizationHelper $forumAuthHelper
    ) {
        $this->formHelper = $formHelper;
        $this->userHelper = $userHelper;
        $this->redirectHelper = $redirectHelper;
        $this->templateHelper = $templateHelper;
        $this->forumAuthHelper = $forumAuthHelper;
    }

    /**
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param int $id
     * @param string $action
     * @return Response|RedirectResponse
     */
    public function indexAction(Request $request, int $id, string $action)
    {
        $discussion = $this->getDiscussion($id);
        if (!in_array($this->userHelper->getUser(), $discussion->forum->getModerators())) {
            throw new AccessDeniedHttpException();
        }

        if ($action === self::ACTION_CLOSE && !$discussion->locked) {
            $discussion->locked = true;
            $this->formHelper->getDoctrine()->getManager()->flush();
        } elseif ($action === self::ACTION_OPEN && $discussion->locked) {
            $discussion->locked = false;
            $this->formHelper->getDoctrine()->getManager()->flush();
        } elseif ($action === self::ACTION_MOVE) {
            $form = $this->formHelper->getFactory()->create(ForumDiscussionMove::class, $discussion);
            $form->handleRequest($request);
            if (!$form->isSubmitted() || !$form->isValid()) {
                return $this->templateHelper->render('forum/discussionMove.html.twig', [
                    TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $discussion->title,
                    TemplateHelper::PARAMETER_DISCUSSION => $discussion,
                    TemplateHelper::PARAMETER_FORM => $form->createView()
                ]);
            }
            $this->formHelper->getDoctrine()->getManager()->flush();
        }

        return $this->redirectHelper->redirectToRoute(
            RouteGenerics::ROUTE_FORUM_DISCUSSION,
            ['id' => $discussion->getId(), 'name' => urlencode($discussion->title)]
        );
    }

    /**
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param int $id1
     * @param int $id2
     * @return RedirectResponse|Response
     */
    public function combineAction(Request $request, int $id1, int $id2)
    {

        $discussion1 = $this->getDiscussion($id1);
        $discussion2 = $this->getDiscussion($id2);

        if ($discussion1->forum !== $discussion2->forum
            || !in_array($this->userHelper->getUser(), $discussion1->forum->getModerators())
        ) {
            throw new AccessDeniedHttpException();
        }

        $newDiscussion = new ForumDiscussion();
        $newDiscussion->forum = $discussion1->forum;

        $form = $this->formHelper->getFactory()->create(ForumDiscussionCombine::class, $newDiscussion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $oldestPost = $this->movePostsAndGetOldest($discussion1, $discussion2, $newDiscussion);

            $newDiscussion->author = $oldestPost->author;
            $newDiscussion->title = $form->get('title')->getData();
            $newDiscussion->viewed = (int)$discussion1->viewed + (int)$discussion2->viewed;
            $this->formHelper->getDoctrine()->getManager()->persist($newDiscussion);
            $this->formHelper->getDoctrine()->getManager()->remove($discussion1);
            $this->formHelper->getDoctrine()->getManager()->remove($discussion2);

            return $this->formHelper->finishFormHandling('', RouteGenerics::ROUTE_FORUM_DISCUSSION, [
                'id' => $newDiscussion->getId(),
                'name' => urlencode($newDiscussion->title)
            ]);
        }

        return $this->templateHelper->render('forum/discussionCombine.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $discussion1->title,
            'discussion1' => $discussion1,
            'discussion2' => $discussion2,
            TemplateHelper::PARAMETER_FORM => $form->createView()
        ]);
    }

    /**
     * @param ForumDiscussion $discussion1
     * @param ForumDiscussion $discussion2
     * @param ForumDiscussion $newDiscussion
     * @return ForumPost
     */
    private function movePostsAndGetOldest(
        ForumDiscussion $discussion1,
        ForumDiscussion $discussion2,
        ForumDiscussion $newDiscussion
    ): ForumPost {
        /**
         * @var ForumPost $oldestPost
         */
        $oldestPost = null;
        foreach ($discussion1->getPosts() as $post) {
            if (is_null($oldestPost) || $post->timestamp < $oldestPost->timestamp) {
                $oldestPost = $post;
            }
            $post->discussion = $newDiscussion;
        }
        foreach ($discussion2->getPosts() as $post) {
            if (is_null($oldestPost) || $post->timestamp < $oldestPost->timestamp) {
                $oldestPost = $post;
            }
            $post->discussion = $newDiscussion;
        }

        // Move the favorites
        foreach ($discussion1->getFavorites() as $favorite) {
            $favorite->discussion = $newDiscussion;
        }
        foreach ($discussion2->getFavorites() as $favorite) {
            $favorite->discussion = $newDiscussion;
        }

        // Move the wikis
        foreach ($discussion1->getWikis() as $wiki) {
            $wiki->discussion = $newDiscussion;
        }
        foreach ($discussion2->getWikis() as $wiki) {
            $wiki->discussion = $newDiscussion;
        }

        return $oldestPost;
    }

    /**
     * @IsGranted("ROLE_USER")
     * @param int $id
     * @param string $postIds
     * @return RedirectResponse
     */
    public function splitAction(int $id, string $postIds): RedirectResponse
    {
        $discussion = $this->getDiscussion($id);
        if (!in_array($this->userHelper->getUser(), $discussion->forum->getModerators())) {
            throw new AccessDeniedHttpException();
        }

        $postIds = array_filter(explode(',', $postIds));
        $firstPost = $this->formHelper->getDoctrine()->getRepository(ForumPost::class)->find($postIds[0]);

        // Create the new discussion
        $newDiscussion = new ForumDiscussion();
        $newDiscussion->forum = $discussion->forum;
        $newDiscussion->title = 'Verwijderd uit ' . $discussion->title;
        $newDiscussion->author = $firstPost->author;
        $this->formHelper->getDoctrine()->getManager()->persist($newDiscussion);

        foreach ($postIds as $postId) {
            $post = $this->formHelper->getDoctrine()->getRepository(ForumPost::class)->find($postId);
            $post->discussion = $newDiscussion;
        }

        $this->formHelper->getDoctrine()->getManager()->flush();

        return $this->redirectHelper->redirectToRoute(
            RouteGenerics::ROUTE_FORUM_DISCUSSION,
            ['id' => $newDiscussion->getId(), 'name' => urlencode($newDiscussion->title)]
        );
    }

    /**
     * @param int $id
     * @return ForumDiscussion
     */
    private function getDiscussion(int $id): ForumDiscussion
    {
        /**
         * @var ForumDiscussion $discussion
         */
        $discussion = $this->formHelper->getDoctrine()->getRepository(ForumDiscussion::class)->find($id);
        if (is_null($discussion) || !$this->forumAuthHelper->userIsModerator($discussion)) {
            throw new AccessDeniedHttpException();
        }
        return $discussion;
    }
}
