<?php

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
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ForumDiscussionController
{
    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var ForumAuthorizationHelper
     */
    private ForumAuthorizationHelper $forumAuthHelper;

    /**
     * @var ForumDiscussionHelper
     */
    private ForumDiscussionHelper $discussionHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @param UserHelper $userHelper
     * @param FormHelper $formHelper
     * @param ForumAuthorizationHelper $forumAuthHelper
     * @param ForumDiscussionHelper $discussionHelper
     * @param TemplateHelper $templateHelper
     */
    public function __construct(
        UserHelper $userHelper,
        FormHelper $formHelper,
        ForumAuthorizationHelper $forumAuthHelper,
        ForumDiscussionHelper $discussionHelper,
        TemplateHelper $templateHelper
    ) {
        $this->userHelper = $userHelper;
        $this->formHelper = $formHelper;
        $this->forumAuthHelper = $forumAuthHelper;
        $this->discussionHelper = $discussionHelper;
        $this->templateHelper = $templateHelper;
    }

    /**
     * @param Request $request
     * @param int $id
     * @param int|null $pageNumber
     * @param int|null $postId
     * @return Response|RedirectResponse
     * @throws Exception
     */
    public function indexAction(Request $request, int $id, int $pageNumber = null, int $postId = null): Response
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
        $posts = $this->discussionHelper->getPosts($newToOld, $pageNumber, $postId);

        return $this->templateHelper->render('forum/discussion.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $discussion->title,
            'userIsModerator' =>
                $this->forumAuthHelper->userIsModerator($discussion->forum, $this->userHelper->getUser()),
            TemplateHelper::PARAMETER_DISCUSSION => $discussion,
            'numberOfPages' => $this->discussionHelper->getNumberOfPages(),
            'numberOfPosts' => $this->discussionHelper->getNumberOfPosts(),
            'pageNumber' => $this->discussionHelper->getPageNumber(),
            'newToOld' => $newToOld,
            'posts' => $posts,
            'mayPost' => $this->forumAuthHelper->mayPost($discussion->forum, $this->userHelper->getUser()),
            'numberOfReadPosts' => $this->discussionHelper->getNumberOfReadPosts(),
            'forumBanner' => $this->getForumBanner($request),
            'forumJump' => $this->discussionHelper->getForumJump(),
        ]);
    }

    /**
     * @param Request $request
     * @return Banner|null
     * @throws Exception
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
         * @var Banner $forumBanner
         */
        $forumBanner = $banners[\random_int(0, \count($banners) - 1)];

        // Create a view for this banner
        $bannerView = new BannerView();
        $bannerView->banner = $forumBanner;
        $bannerView->timestamp = new \DateTime();
        $bannerView->ipAddress = \inet_pton($request->getClientIp());
        $this->formHelper->getDoctrine()->getManager()->persist($bannerView);
        $this->formHelper->getDoctrine()->getManager()->flush();

        return $forumBanner;
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function newAction(Request $request, int $id)
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        /**
         * @var ForumForum $forum
         */
        $forum = $this->formHelper->getDoctrine()->getRepository(ForumForum::class)->find($id);
        if (null === $forum || !$this->forumAuthHelper->mayPost($forum, $this->userHelper->getUser())) {
            return $this->formHelper->getRedirectHelper()->redirectToRoute(RouteGenerics::ROUTE_FORUM);
        }

        $forumDiscussion = new ForumDiscussion();
        $forumDiscussion->forum = $forum;
        $forumDiscussion->author = $this->userHelper->getUser();

        $form = $this->formHelper->getFactory()->create(ForumDiscussionForm::class, $forumDiscussion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->formHelper->addPost(
                $forumDiscussion,
                $this->userHelper->getUser(),
                $form->get('signatureOn')->getData(),
                $form->get('text')->getData()
            );
            $this->formHelper->getDoctrine()->getManager()->persist($forumDiscussion);
            $this->formHelper->getDoctrine()->getManager()->flush();

            return $this->formHelper->finishFormHandling('', RouteGenerics::ROUTE_FORUM_DISCUSSION, [
                'id' => $forumDiscussion->id,
                'name' => \urlencode($forumDiscussion->title)
            ]);
        }

        return $this->templateHelper->render('forum/newDiscussion.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $forum->name,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
            TemplateHelper::PARAMETER_FORUM => $forum
        ]);
    }
}
