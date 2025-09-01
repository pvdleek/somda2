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
use Symfony\Component\String\Slugger\SluggerInterface;

class ForumModerateController
{
    public const ACTION_CLOSE = 'close';
    public const ACTION_OPEN = 'open';
    public const ACTION_MOVE = 'move';

    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly FormHelper $form_helper,
        private readonly UserHelper $user_helper,
        private readonly TemplateHelper $template_helper,
        private readonly ForumAuthorizationHelper $forum_authorization_helper,
    ) {
    }

    public function indexAction(Request $request, int $id, string $action): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $discussion = $this->getDiscussion($id);

        if ($action === self::ACTION_CLOSE && !$discussion->locked) {
            $discussion->locked = true;
            $this->form_helper->getDoctrine()->getManager()->flush();
        } elseif ($action === self::ACTION_OPEN && $discussion->locked) {
            $discussion->locked = false;
            $this->form_helper->getDoctrine()->getManager()->flush();
        } elseif ($action === self::ACTION_MOVE) {
            $form = $this->form_helper->getFactory()->create(ForumDiscussionMove::class, $discussion);
            $form->handleRequest($request);
            if (!$form->isSubmitted() || !$form->isValid()) {
                return $this->template_helper->render('forum/discussionMove.html.twig', [
                    TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $discussion->title,
                    TemplateHelper::PARAMETER_DISCUSSION => $discussion,
                    TemplateHelper::PARAMETER_FORM => $form->createView()
                ]);
            }
            $this->form_helper->getDoctrine()->getManager()->flush();
        }

        return $this->form_helper->getRedirectHelper()->redirectToRoute(
            RouteGenerics::ROUTE_FORUM_DISCUSSION,
            ['id' => $discussion->id, 'name' => $this->slugger->slug($discussion->title)]
        );
    }

    public function combineAction(Request $request, int $id1, int $id2): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $discussion1 = $this->getDiscussion($id1);
        $discussion2 = $this->getDiscussion($id2);

        if ($discussion1->forum !== $discussion2->forum) {
            throw new AccessDeniedException("The forums of the discussions to be combined do not match");
        }

        $new_discussion = (new ForumDiscussion());
        $new_discussion->forum = $discussion1->forum;
        
        $form = $this->form_helper->getFactory()->create(ForumDiscussionCombine::class, $new_discussion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $oldestPost = $this->movePostsAndGetOldest($discussion1, $discussion2, $new_discussion);

            $new_discussion->author = $oldestPost->author;
            $new_discussion->title = $form->get('title')->getData();
            $new_discussion->viewed = (int) $discussion1->viewed + (int) $discussion2->viewed;
            $this->form_helper->getDoctrine()->getManager()->persist($new_discussion);
            $this->form_helper->getDoctrine()->getManager()->remove($discussion1);
            $this->form_helper->getDoctrine()->getManager()->remove($discussion2);
            $this->form_helper->getDoctrine()->getManager()->flush();

            return $this->form_helper->finishFormHandling('', RouteGenerics::ROUTE_FORUM_DISCUSSION, [
                'id' => $new_discussion->id,
                'name' => $this->slugger->slug($new_discussion->title)
            ]);
        }

        return $this->template_helper->render('forum/discussionCombine.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $discussion1->title,
            'discussion1' => $discussion1,
            'discussion2' => $discussion2,
            TemplateHelper::PARAMETER_FORM => $form->createView()
        ]);
    }

    private function movePostsAndGetOldest(ForumDiscussion $discussion1, ForumDiscussion $discussion2, ForumDiscussion $new_discussion): ForumPost {
        /**
         * @var ForumPost|null $oldest_post
         */
        $oldest_post = null;
        foreach ($discussion1->getPosts() as $post) {
            if (null === $oldest_post || $post->timestamp < $oldest_post->timestamp) {
                $oldest_post = $post;
            }
            $post->discussion = $new_discussion;
        }
        foreach ($discussion2->getPosts() as $post) {
            if (null === $oldest_post || $post->timestamp < $oldest_post->timestamp) {
                $oldest_post = $post;
            }
            $post->discussion = $new_discussion;
        }

        // Move the favorites
        foreach ($discussion1->getFavorites() as $favorite) {
            $favorite->discussion = $new_discussion;
        }
        foreach ($discussion2->getFavorites() as $favorite) {
            $favorite->discussion = $new_discussion;
        }

        // Move the wikis
        foreach ($discussion1->getWikis() as $wiki) {
            $wiki->discussion = $new_discussion;
        }
        foreach ($discussion2->getWikis() as $wiki) {
            $wiki->discussion = $new_discussion;
        }

        return $oldest_post;
    }

    public function splitAction(int $id, string $post_ids): RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $discussion = $this->getDiscussion($id);

        $post_ids = \array_filter(\explode(',', $post_ids));
        $first_post = $this->form_helper->getDoctrine()->getRepository(ForumPost::class)->find($post_ids[0]);

        $new_discussion = new ForumDiscussion();
        $new_discussion->forum = $discussion->forum;
        $new_discussion->title = \substr('Verwijderd uit ' . $discussion->title, 0, 75);
        $new_discussion->author = $first_post->author;
        $this->form_helper->getDoctrine()->getManager()->persist($new_discussion);

        foreach ($post_ids as $post_id) {
            $post = $this->form_helper->getDoctrine()->getRepository(ForumPost::class)->find($post_id);
            $post->discussion = $new_discussion;
        }

        $this->form_helper->getDoctrine()->getManager()->flush();

        return $this->form_helper->getRedirectHelper()->redirectToRoute(
            RouteGenerics::ROUTE_FORUM_DISCUSSION,
            ['id' => $new_discussion->id, 'name' => $this->slugger->slug($new_discussion->title)]
        );
    }

    private function getDiscussion(int $id): ForumDiscussion
    {
        /**
         * @var ForumDiscussion $discussion
         */
        $discussion = $this->form_helper->getDoctrine()->getRepository(ForumDiscussion::class)->find($id);
        if (null === $discussion || !$this->forum_authorization_helper->userIsModerator($discussion->forum, $this->user_helper->getUser())) {
            throw new AccessDeniedException('The discussion does not exist of the user cannot moderate it');
        }
        return $discussion;
    }
}
