<?php

namespace App\Controller;

use App\Entity\Banner;
use App\Entity\BannerView;
use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Entity\ForumPost;
use App\Form\ForumDiscussion as ForumDiscussionForm;
use App\Form\ForumPost as ForumPostForm;
use App\Generics\RouteGenerics;
use App\Helpers\FormHelper;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\RedirectHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ForumDiscussionController
{
    public const MAX_POSTS_PER_PAGE = 100;

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
     * @var RedirectHelper
     */
    private RedirectHelper $redirectHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @param UserHelper $userHelper
     * @param FormHelper $formHelper
     * @param ForumAuthorizationHelper $forumAuthHelper
     * @param RedirectHelper $redirectHelper
     * @param TemplateHelper $templateHelper
     */
    public function __construct(
        UserHelper $userHelper,
        FormHelper $formHelper,
        ForumAuthorizationHelper $forumAuthHelper,
        RedirectHelper $redirectHelper,
        TemplateHelper $templateHelper
    ) {
        $this->userHelper = $userHelper;
        $this->formHelper = $formHelper;
        $this->forumAuthHelper = $forumAuthHelper;
        $this->redirectHelper = $redirectHelper;
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
        if (is_null($discussion)) {
            return $this->redirectHelper->redirectToRoute(RouteGenerics::ROUTE_FORUM);
        }
        if (!$this->forumAuthHelper->mayView($discussion->forum, $this->userHelper->getUser())) {
            throw new AccessDeniedHttpException();
        }

        $discussion->viewed = (int)$discussion->viewed + 1;
        $this->formHelper->getDoctrine()->getManager()->flush();

        $numberOfPosts = $this->formHelper->getDoctrine()->getRepository(ForumDiscussion::class)->getNumberOfPosts(
            $discussion
        );
        $numberOfPages = floor(($numberOfPosts - 1) / self::MAX_POSTS_PER_PAGE) + 1;

        /**
         * @var ForumPost[] $posts
         */
        $posts = [];
        $numberOfReadPosts = 0;
        $forumJump = null;
        if (is_null($pageNumber) && is_null($postId) && $this->userHelper->userIsLoggedIn()) {
            // Neither a specific page or post were requested but the user is logged in,
            // so we will go to the first unread post in the discussion
            if ($discussion->forum->type === ForumForum::TYPE_ARCHIVE) {
                $pageNumber = 1;
            } else {
                $numberOfReadPosts = $this->formHelper
                    ->getDoctrine()
                    ->getRepository(ForumDiscussion::class)
                    ->getNumberOfReadPosts($discussion, $this->userHelper->getUser());
                $pageNumber = floor($numberOfReadPosts / self::MAX_POSTS_PER_PAGE) + 1;
                $forumJump = 'new_post';
            }
            $posts = $this->formHelper->getDoctrine()->getRepository(ForumPost::class)->findBy(
                [ForumPostForm::FIELD_DISCUSSION => $discussion],
                [ForumPostForm::FIELD_TIMESTAMP => 'ASC'],
                self::MAX_POSTS_PER_PAGE,
                ($pageNumber - 1) * self::MAX_POSTS_PER_PAGE
            );
            $this->formHelper->getDoctrine()->getRepository(ForumDiscussion::class)->markPostsAsRead(
                $this->userHelper->getUser(),
                $posts
            );
        } elseif (is_null($pageNumber) && !is_null($postId)) {
            // A specific post was requested, so we go to this post
            $postNumber = $this->formHelper
                ->getDoctrine()
                ->getRepository(ForumDiscussion::class)
                ->getPostNumberInDiscussion($discussion, $postId);
            $pageNumber = floor($postNumber / self::MAX_POSTS_PER_PAGE) + 1;
            $forumJump = 'p' . $postId;
        }

        if (count($posts) < 1) {
            if (is_null($pageNumber) || $pageNumber < 1) {
                $pageNumber = 1;
            }
            $posts = $this->formHelper->getDoctrine()->getRepository(ForumPost::class)->findBy(
                [ForumPostForm::FIELD_DISCUSSION => $discussion],
                [ForumPostForm::FIELD_TIMESTAMP => 'ASC'],
                self::MAX_POSTS_PER_PAGE,
                ($pageNumber - 1) * self::MAX_POSTS_PER_PAGE
            );
        }

        return $this->templateHelper->render('forum/discussion.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $discussion->title,
            'userIsModerator' => $this->forumAuthHelper->userIsModerator($discussion, $this->userHelper->getUser()),
            TemplateHelper::PARAMETER_DISCUSSION => $discussion,
            'numberOfPages' => $numberOfPages,
            'pageNumber' => $pageNumber,
            'posts' => $posts,
            'mayPost' => $this->forumAuthHelper->mayPost($discussion->forum, $this->userHelper->getUser()),
            'numberOfReadPosts' => $numberOfReadPosts,
            'forumBanner' => $this->getForumBanner($request),
            'forumJump' => $forumJump,
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
        if (count($banners) < 1) {
            return null;
        }
        /**
         * @var Banner $forumBanner
         */
        $forumBanner = $banners[random_int(0, count($banners) - 1)];

        // Create a view for this banner
        $bannerView = new BannerView();
        $bannerView->banner = $forumBanner;
        $bannerView->timestamp = new DateTime();
        $bannerView->ipAddress = inet_pton($request->getClientIp());
        $this->formHelper->getDoctrine()->getManager()->persist($bannerView);
        $this->formHelper->getDoctrine()->getManager()->flush();

        return $forumBanner;
    }

    /**
     * @IsGranted("ROLE_USER")
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
        $forum = $this->formHelper->getDoctrine()->getRepository(ForumForum::class)->find($id);
        if (is_null($forum) || !$this->forumAuthHelper->mayPost($forum, $this->userHelper->getUser())) {
            return $this->redirectHelper->redirectToRoute(RouteGenerics::ROUTE_FORUM);
        }

        $forumDiscussion = new ForumDiscussion();
        $forumDiscussion->forum = $forum;
        $forumDiscussion->author = $this->userHelper->getUser();

        $form = $this->formHelper->getFactory()->create(ForumDiscussionForm::class, $forumDiscussion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->formHelper->addPost($form, $forumDiscussion, $this->userHelper->getUser());
            $this->formHelper->getDoctrine()->getManager()->persist($forumDiscussion);
            $this->formHelper->getDoctrine()->getManager()->flush();

            return $this->formHelper->finishFormHandling('', RouteGenerics::ROUTE_FORUM_DISCUSSION, [
                'id' => $forumDiscussion->getId(),
                'name' => urlencode($forumDiscussion->title)
            ]);
        }

        return $this->templateHelper->render('forum/newDiscussion.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $forum->name,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
            TemplateHelper::PARAMETER_FORUM => $forum
        ]);
    }
}
