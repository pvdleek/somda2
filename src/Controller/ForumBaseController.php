<?php

namespace App\Controller;

use App\Entity\Banner;
use App\Entity\BannerView;
use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
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
        if ($forum->getType() === 0) {
            return true;
        }
        if (in_array($forum->getType(), [1, 2, 4])) {
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
        if (!$this->mayView($forum) || $forum->getType() === 4) {
            return false;
        }
        if (in_array($forum->getType(), [0, 1])) {
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
}
