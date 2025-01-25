<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\ForumDiscussion;
use App\Entity\ForumPost;
use App\Form\ForumDiscussionCombine;
use App\Form\ForumDiscussionMove;
use App\Generics\RoleGenerics;
use App\Generics\RouteGenerics;
use App\Helpers\FormHelper;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ForumModerateController
{
    public const ACTION_CLOSE = 'close';
    public const ACTION_OPEN = 'open';
    public const ACTION_MOVE = 'move';

    public function __construct(
        private readonly FormHelper $formHelper,
        private readonly UserHelper $userHelper,
        private readonly TemplateHelper $templateHelper,
        private readonly ForumAuthorizationHelper $forumAuthHelper,
    ) {
    }

    public function indexAction(Request $request, int $id, string $action): Response|RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $discussion = $this->getDiscussion($id);

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

        return $this->formHelper->getRedirectHelper()->redirectToRoute(
            RouteGenerics::ROUTE_FORUM_DISCUSSION,
            ['id' => $discussion->id, 'name' => urlencode($discussion->title)]
        );
    }

    public function combineAction(Request $request, int $id1, int $id2): Response|RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $discussion1 = $this->getDiscussion($id1);
        $discussion2 = $this->getDiscussion($id2);

        if ($discussion1->forum !== $discussion2->forum) {
            throw new AccessDeniedException("The forums of the discussions to be combined do not match");
        }

        $newDiscussion = new ForumDiscussion();
        $newDiscussion->forum = $discussion1->forum;

        $form = $this->formHelper->getFactory()->create(ForumDiscussionCombine::class, $newDiscussion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $oldestPost = $this->movePostsAndGetOldest($discussion1, $discussion2, $newDiscussion);

            $newDiscussion->author = $oldestPost->author;
            $newDiscussion->title = $form->get('title')->getData();
            $newDiscussion->viewed = (int) $discussion1->viewed + (int) $discussion2->viewed;
            $this->formHelper->getDoctrine()->getManager()->persist($newDiscussion);
            $this->formHelper->getDoctrine()->getManager()->remove($discussion1);
            $this->formHelper->getDoctrine()->getManager()->remove($discussion2);
            $this->formHelper->getDoctrine()->getManager()->flush();

            return $this->formHelper->finishFormHandling('', RouteGenerics::ROUTE_FORUM_DISCUSSION, [
                'id' => $newDiscussion->id,
                'name' => \urlencode($newDiscussion->title)
            ]);
        }

        return $this->templateHelper->render('forum/discussionCombine.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $discussion1->title,
            'discussion1' => $discussion1,
            'discussion2' => $discussion2,
            TemplateHelper::PARAMETER_FORM => $form->createView()
        ]);
    }

    private function movePostsAndGetOldest(ForumDiscussion $discussion1, ForumDiscussion $discussion2, ForumDiscussion $newDiscussion): ForumPost {
        /**
         * @var ForumPost $oldestPost
         */
        $oldestPost = null;
        foreach ($discussion1->getPosts() as $post) {
            if (null === $oldestPost || $post->timestamp < $oldestPost->timestamp) {
                $oldestPost = $post;
            }
            $post->discussion = $newDiscussion;
        }
        foreach ($discussion2->getPosts() as $post) {
            if (null === $oldestPost || $post->timestamp < $oldestPost->timestamp) {
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

    public function splitAction(int $id, string $postIds): RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $discussion = $this->getDiscussion($id);

        $postIds = \array_filter(\explode(',', $postIds));
        $firstPost = $this->formHelper->getDoctrine()->getRepository(ForumPost::class)->find($postIds[0]);

        // Create the new discussion
        $newDiscussion = new ForumDiscussion();
        $newDiscussion->forum = $discussion->forum;
        $newDiscussion->title = \substr('Verwijderd uit ' . $discussion->title, 0, 75);
        $newDiscussion->author = $firstPost->author;
        $this->formHelper->getDoctrine()->getManager()->persist($newDiscussion);

        foreach ($postIds as $postId) {
            $post = $this->formHelper->getDoctrine()->getRepository(ForumPost::class)->find($postId);
            $post->discussion = $newDiscussion;
        }

        $this->formHelper->getDoctrine()->getManager()->flush();

        return $this->formHelper->getRedirectHelper()->redirectToRoute(
            RouteGenerics::ROUTE_FORUM_DISCUSSION,
            ['id' => $newDiscussion->id, 'name' => urlencode($newDiscussion->title)]
        );
    }

    private function getDiscussion(int $id): ForumDiscussion
    {
        /**
         * @var ForumDiscussion $discussion
         */
        $discussion = $this->formHelper->getDoctrine()->getRepository(ForumDiscussion::class)->find($id);
        if (null === $discussion
            || !$this->forumAuthHelper->userIsModerator($discussion->forum, $this->userHelper->getUser())
        ) {
            throw new AccessDeniedException('The discussion does not exist of the user cannot moderate it');
        }
        return $discussion;
    }
}
