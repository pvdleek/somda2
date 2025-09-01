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
        private readonly UserHelper $userHelper,
        private readonly FormHelper $formHelper,
        private readonly ForumAuthorizationHelper $forumAuthHelper,
        private readonly ForumDiscussionHelper $discussionHelper,
        private readonly TemplateHelper $templateHelper,
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
        $discussion = $this->formHelper->getDoctrine()->getRepository(ForumDiscussion::class)->find($id);
        if (null === $discussion) {
            return $this->formHelper->getRedirectHelper()->redirectToRoute(RouteGenerics::ROUTE_FORUM);
        }

        $newToOld = $this->userHelper->userIsLoggedIn() ?
            (bool)$this->userHelper->getPreferenceByKey(UserPreference::KEY_FORUM_NEW_TO_OLD)->value : false;

        $this->discussionHelper->setDiscussion($discussion);
        $posts = $this->discussionHelper->getPosts($newToOld, $page_number, $post_id);

        return $this->templateHelper->render('forum/discussion.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $discussion->title,
            'userIsModerator' =>
                $this->forumAuthHelper->userIsModerator($discussion->forum, $this->userHelper->getUser()),
            TemplateHelper::PARAMETER_DISCUSSION => $discussion,
            'numberOfPages' => $this->discussionHelper->getNumberOfPages(),
            'number_of_posts' => $this->discussionHelper->getNumberOfPosts(),
            'page_number' => $this->discussionHelper->getPageNumber(),
            'newToOld' => $newToOld,
            'posts' => $posts,
            'mayPost' => $this->forumAuthHelper->mayPost($discussion->forum, $this->userHelper->getUser()),
            'numberOfReadPosts' => $this->discussionHelper->getNumberOfReadPosts(),
            'forumBanner' => $this->getForumBanner($request),
            'forumJump' => $this->discussionHelper->getForumJump(),
        ]);
    }

    /**
     * @throws \Exception
     */
    private function getForumBanner(Request $request): ?Banner
    {
        $banners = $this->formHelper->getDoctrine()->getRepository(Banner::class)->findBy(
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
        $this->formHelper->getDoctrine()->getManager()->persist($banner_view);
        $this->formHelper->getDoctrine()->getManager()->flush();

        return $forum_banner;
    }

    /**
     * @throws \Exception
     */
    public function newAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        /**
         * @var ForumForum $forum
         */
        $forum = $this->formHelper->getDoctrine()->getRepository(ForumForum::class)->find($id);
        if (null === $forum || !$this->forumAuthHelper->mayPost($forum, $this->userHelper->getUser())) {
            return $this->formHelper->getRedirectHelper()->redirectToRoute(RouteGenerics::ROUTE_FORUM);
        }

        $forum_discussion = new ForumDiscussion();
        $forum_discussion->forum = $forum;
        $forum_discussion->author = $this->userHelper->getUser();

        $form = $this->formHelper->getFactory()->create(ForumDiscussionForm::class, $forum_discussion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->formHelper->addPost(
                $forum_discussion,
                $this->userHelper->getUser(),
                $form->get('signature_on')->getData(),
                $form->get('text')->getData()
            );
            $this->formHelper->getDoctrine()->getManager()->persist($forum_discussion);
            $this->formHelper->getDoctrine()->getManager()->flush();

            return $this->formHelper->finishFormHandling('', RouteGenerics::ROUTE_FORUM_DISCUSSION, [
                'id' => $forum_discussion->id,
                'name' => $this->slugger->slug($forum_discussion->title)
            ]);
        }

        return $this->templateHelper->render('forum/newDiscussion.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $forum->name,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
            TemplateHelper::PARAMETER_FORUM => $forum
        ]);
    }
}
