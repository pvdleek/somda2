<?php

namespace App\Controller;

use App\Entity\Banner;
use App\Entity\BannerView;
use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Entity\ForumPost;
use App\Entity\ForumPostLog;
use App\Entity\ForumPostText;
use DateTime;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class ForumBaseController extends BaseController
{
    public const MAX_POSTS_PER_PAGE = 100;

    /**
     * @param Request $request
     * @return Banner|null
     */
    protected function getForumBanner(Request $request): ?Banner
    {
        $banners = $this->doctrine->getRepository(Banner::class)->findBy(
            ['location' => Banner::LOCATION_FORUM, 'active' => true]
        );
        if (count($banners) < 1) {
            return null;
        }
        /**
         * @var Banner $forumBanner
         */
        $forumBanner = $banners[rand(0, count($banners) - 1)];

        // Create a view for this banner
        $bannerView = new BannerView();
        $bannerView->banner = $forumBanner;
        $bannerView->timestamp = new DateTime();
        $bannerView->ipAddress = inet_pton($request->getClientIp());
        $this->doctrine->getManager()->persist($bannerView);
        $this->doctrine->getManager()->flush();

        return $forumBanner;
    }

    /**
     * @param ForumForum $forum
     * @return bool
     */
    protected function mayView(ForumForum $forum): bool
    {
        if ($forum->type === ForumForum::TYPE_PUBLIC) {
            return true;
        }
        if (in_array($forum->type, [ForumForum::TYPE_LOGGED_IN, ForumForum::TYPE_ARCHIVE])) {
            return $this->userIsLoggedIn();
        }
        return in_array($this->getUser(), $forum->getModerators());
    }

    /**
     * @param ForumForum $forum
     * @return bool
     */
    protected function mayPost(ForumForum $forum): bool
    {
        if (!$this->mayView($forum) || $forum->type === ForumForum::TYPE_ARCHIVE) {
            return false;
        }
        if (in_array($forum->type, [ForumForum::TYPE_PUBLIC, ForumForum::TYPE_LOGGED_IN])) {
            return $this->userIsLoggedIn();
        }
        return in_array($this->getUser(), $forum->getModerators());
    }

    /**
     * @param ForumDiscussion $discussion
     * @return bool
     */
    protected function userIsModerator(ForumDiscussion $discussion): bool
    {
        return in_array($this->getUser(), $discussion->forum->getModerators());
    }

    /**
     * @param FormInterface $form
     * @param ForumDiscussion $discussion
     * @throws Exception
     */
    protected function addPost(FormInterface $form, ForumDiscussion $discussion): void
    {
        $post = new ForumPost();
        $post->author = $this->getUser();
        $post->timestamp = new DateTime();
        $post->discussion = $discussion;
        $post->signatureOn = $form->get('signatureOn')->getData();
        $this->doctrine->getManager()->persist($post);

        $postText = new ForumPostText();
        $postText->post = $post;
        $postText->text = $form->get('text')->getData();
        $this->doctrine->getManager()->persist($postText);

        $postLog = new ForumPostLog();
        $postLog->action = ForumPostLog::ACTION_POST_NEW;
        $this->doctrine->getManager()->persist($postLog);

        $post->addLog($postLog);
        $post->text = $postText;
        $discussion->addPost($post);
    }
}
