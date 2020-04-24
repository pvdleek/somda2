<?php

namespace App\Controller;

use App\Entity\Banner;
use App\Entity\BannerView;
use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Entity\ForumPost;
use App\Entity\ForumPostLog;
use App\Entity\ForumPostText;
use App\Form\ForumDiscussion as ForumDiscussionForm;
use App\Helpers\FormHelper;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\RedirectHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use DateTime;
use Exception;
use Symfony\Component\Form\FormInterface;
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
            return $this->redirectHelper->redirectToRoute('forum');
        }
        if (!$this->forumAuthHelper->mayView($discussion->forum, $this->userHelper->getUser())) {
            throw new AccessDeniedHttpException();
        }

        $discussion->viewed = $discussion->viewed + 1;
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
            if ($discussion->forum->type !== ForumForum::TYPE_ARCHIVE) {
                $numberOfReadPosts = $this->formHelper
                    ->getDoctrine()
                    ->getRepository(ForumDiscussion::class)
                    ->getNumberOfReadPosts($discussion, $this->userHelper->getUser());
                $pageNumber = floor($numberOfReadPosts / self::MAX_POSTS_PER_PAGE) + 1;
                $forumJump = 'new_post';
            }
            $posts = $this->formHelper->getDoctrine()->getRepository(ForumPost::class)->findBy(
                ['discussion' => $discussion],
                ['timestamp' => 'ASC'],
                self::MAX_POSTS_PER_PAGE,
                ($pageNumber - 1) * self::MAX_POSTS_PER_PAGE
            );
            $this->formHelper->getDoctrine()->getRepository(ForumDiscussion::class)->markPostsAsRead(
                $this->userHelper->getUser(),
                $posts
            );
        } elseif (!is_null($postId)) {
            // A specific post was requested, so we go to this post
            $postNumber = $this->formHelper
                ->getDoctrine()
                ->getRepository(ForumDiscussion::class)
                ->getPostNumberInDiscussion($discussion, $postId);
            $pageNumber = floor($postNumber / self::MAX_POSTS_PER_PAGE) + 1;
            $forumJump = 'p' . $postId;
        }

        if (count($posts) < 1) {
            $posts = $this->formHelper->getDoctrine()->getRepository(ForumPost::class)->findBy(
                ['discussion' => $discussion],
                ['timestamp' => 'ASC'],
                self::MAX_POSTS_PER_PAGE,
                ($pageNumber - 1) * self::MAX_POSTS_PER_PAGE
            );
        }

        return $this->templateHelper->render('forum/discussion.html.twig', [
            'pageTitle' => 'Forum - ' . $discussion->title,
            'userIsModerator' => $this->forumAuthHelper->userIsModerator($discussion, $this->userHelper->getUser()),
            'discussion' => $discussion,
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
    protected function getForumBanner(Request $request): ?Banner
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
        if (is_null($forum)) {
            return $this->redirectHelper->redirectToRoute('forum');
        }

        $forumDiscussion = new ForumDiscussion();
        $forumDiscussion->forum = $forum;
        $forumDiscussion->author = $this->userHelper->getUser();

        $form = $this->formHelper->getFactory()->create(ForumDiscussionForm::class, $forumDiscussion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addPost($form, $forumDiscussion);
            $this->formHelper->getDoctrine()->getManager()->persist($forumDiscussion);

            return $this->formHelper->finishFormHandling('', 'forum_discussion', [
                'id' => $forumDiscussion->getId(),
                'name' => urlencode($forumDiscussion->title)
            ]);
        }

        return $this->templateHelper->render('forum/newDiscussion.html.twig', [
            'pageTitle' => 'Forum - ' . $forum->name,
            'form' => $form->createView(),
            'forum' => $forum
        ]);
    }

    /**
     * @param FormInterface $form
     * @param ForumDiscussion $discussion
     * @throws Exception
     */
    private function addPost(FormInterface $form, ForumDiscussion $discussion): void
    {
        $post = new ForumPost();
        $post->author = $this->userHelper->getUser();
        $post->timestamp = new DateTime();
        $post->discussion = $discussion;
        $post->signatureOn = $form->get('signatureOn')->getData();
        $this->formHelper->getDoctrine()->getManager()->persist($post);

        $postText = new ForumPostText();
        $postText->post = $post;
        $postText->text = $form->get('text')->getData();
        $this->formHelper->getDoctrine()->getManager()->persist($postText);

        $postLog = new ForumPostLog();
        $postLog->action = ForumPostLog::ACTION_POST_NEW;
        $this->formHelper->getDoctrine()->getManager()->persist($postLog);

        $post->addLog($postLog);
        $post->text = $postText;
        $discussion->addPost($post);
    }
}
