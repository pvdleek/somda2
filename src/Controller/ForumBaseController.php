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
    public const MODERATOR_UID = 2;

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
        $forumBanner = $banners[rand(0, count($banners) - 1)];

        // Create a view for this banner
        $bannerView = new BannerView();
        $bannerView
            ->setBanner($forumBanner)
            ->setTimestamp(time())
            ->setIp(inet_pton($request->getClientIp()));
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
        if ($forum->getType() === ForumForum::TYPE_PUBLIC) {
            return true;
        }
        if (in_array($forum->getType(), [ForumForum::TYPE_LOGGED_IN, ForumForum::TYPE_ARCHIVE])) {
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
        if (!$this->mayView($forum) || $forum->getType() === ForumForum::TYPE_ARCHIVE) {
            return false;
        }
        if (in_array($forum->getType(), [ForumForum::TYPE_PUBLIC, ForumForum::TYPE_LOGGED_IN])) {
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
        return in_array($this->getUser(), $discussion->getForum()->getModerators());
    }

    /**
     * @param FormInterface $form
     * @param ForumDiscussion $discussion
     * @throws Exception
     */
    protected function addPost(FormInterface $form, ForumDiscussion $discussion): void
    {
        $post = new ForumPost();
        $post
            ->setAuthor($this->getUser())
            ->setTimestamp(new DateTime())
            ->setDiscussion($discussion)
            ->setSignatureOn($form->get('signatureOn')->getData());
        $this->doctrine->getManager()->persist($post);

        $postText = new ForumPostText();
        $postText->setPost($post)->setText($form->get('text')->getData());
        $this->doctrine->getManager()->persist($postText);

        $postLog = new ForumPostLog();
        $postLog->setAction(ForumPostLog::ACTION_POST_NEW);
        $this->doctrine->getManager()->persist($postLog);

        $post->addLog($postLog)->setText($postText);
        $discussion->addPost($post);
    }
}
