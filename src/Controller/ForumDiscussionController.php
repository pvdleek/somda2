<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Banner;
use App\Entity\BannerView;
use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Entity\UserPreference;
use App\Form\ForumDiscussion as ForumDiscussionForm;
use App\Generics\RoleGenerics;
use App\Generics\RouteGenerics;
use App\Helpers\FormHelper;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\ForumDiscussionHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class ForumDiscussionController
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly UserHelper $user_helper,
        private readonly FormHelper $form_helper,
        private readonly ForumAuthorizationHelper $forum_authorization_helper,
        private readonly ForumDiscussionHelper $discussion_helper,
        private readonly TemplateHelper $template_helper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function indexAction(Request $request, int $id, ?int $page_number = null, ?int $post_id = null): Response|RedirectResponse
    {
        /**
         * @var ForumDiscussion $discussion
         */
        $discussion = $this->form_helper->getDoctrine()->getRepository(ForumDiscussion::class)->find($id);
        if (null === $discussion) {
            return $this->form_helper->getRedirectHelper()->redirectToRoute(RouteGenerics::ROUTE_FORUM);
        }

        $newToOld = $this->user_helper->userIsLoggedIn() ?
            (bool)$this->user_helper->getPreferenceByKey(UserPreference::KEY_FORUM_NEW_TO_OLD)->value : false;

        $this->discussion_helper->setDiscussion($discussion);
        $posts = $this->discussion_helper->getPosts($newToOld, $page_number, $post_id);

        return $this->template_helper->render('forum/discussion.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $discussion->title,
            'userIsModerator' =>
                $this->forum_authorization_helper->userIsModerator($discussion->forum, $this->user_helper->getUser()),
            TemplateHelper::PARAMETER_DISCUSSION => $discussion,
            'numberOfPages' => $this->discussion_helper->getNumberOfPages(),
            'number_of_posts' => $this->discussion_helper->getNumberOfPosts(),
            'page_number' => $this->discussion_helper->getPageNumber(),
            'newToOld' => $newToOld,
            'posts' => $posts,
            'mayPost' => $this->forum_authorization_helper->mayPost($discussion->forum, $this->user_helper->getUser()),
            'numberOfReadPosts' => $this->discussion_helper->getNumberOfReadPosts(),
            'forumBanner' => $this->getForumBanner($request),
            'forumJump' => $this->discussion_helper->getForumJump(),
        ]);
    }

    /**
     * @throws \Exception
     */
    private function getForumBanner(Request $request): ?Banner
    {
        $banners = $this->form_helper->getDoctrine()->getRepository(Banner::class)->findBy(
            ['location' => Banner::LOCATION_FORUM, 'active' => true]
        );
        if (\count($banners) < 1) {
            return null;
        }
        /**
         * @var Banner $forum_banner
         */
        $forum_banner = $banners[\random_int(0, \count($banners) - 1)];

        // Create a view for this banner
        $banner_view = new BannerView();
        $banner_view->banner = $forum_banner;
        $banner_view->timestamp = new \DateTime();
        $banner_view->ip_address = (int) \inet_pton($request->getClientIp());
        $this->form_helper->getDoctrine()->getManager()->persist($banner_view);
        $this->form_helper->getDoctrine()->getManager()->flush();

        return $forum_banner;
    }

    /**
     * @throws \Exception
     */
    public function newAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        /**
         * @var ForumForum $forum
         */
        $forum = $this->form_helper->getDoctrine()->getRepository(ForumForum::class)->find($id);
        if (null === $forum || !$this->forum_authorization_helper->mayPost($forum, $this->user_helper->getUser())) {
            return $this->form_helper->getRedirectHelper()->redirectToRoute(RouteGenerics::ROUTE_FORUM);
        }

        $forum_discussion = new ForumDiscussion();
        $forum_discussion->forum = $forum;
        $forum_discussion->author = $this->user_helper->getUser();

        $form = $this->form_helper->getFactory()->create(ForumDiscussionForm::class, $forum_discussion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->form_helper->addPost(
                $forum_discussion,
                $this->user_helper->getUser(),
                $form->get('signature_on')->getData(),
                $form->get('text')->getData()
            );
            $this->form_helper->getDoctrine()->getManager()->persist($forum_discussion);
            $this->form_helper->getDoctrine()->getManager()->flush();

            return $this->form_helper->finishFormHandling('', RouteGenerics::ROUTE_FORUM_DISCUSSION, [
                'id' => $forum_discussion->id,
                'name' => $this->slugger->slug($forum_discussion->title)
            ]);
        }

        return $this->template_helper->render('forum/newDiscussion.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $forum->name,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
            TemplateHelper::PARAMETER_FORUM => $forum
        ]);
    }
}
