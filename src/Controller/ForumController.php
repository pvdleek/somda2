<?php

namespace App\Controller;

use App\Entity\Banner;
use App\Entity\BannerView;
use App\Entity\ForumCategory;
use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Entity\ForumPost;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ForumController extends BaseController
{
    public const MAX_POSTS_PER_PAGE = 100;

    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        $this->breadcrumbHelper->addPart('general.navigation.forum.index', 'forum', [], true);

        $forumCategories = $this->doctrine->getRepository(ForumCategory::class)->findBy([], ['order' => 'ASC']);
        $categories = [];
        foreach ($forumCategories as $category) {
            $categories[] = [
                'category' => $category,
                'forums' => $this->doctrine->getRepository(ForumForum::class)->findByCategory($category),
            ];
        }

        return $this->render('forum/index.html.twig', ['categories' => $categories]);
    }

    /**
     * @param int $id
     * @return Response|RedirectResponse
     */
    public function forumAction(int $id): Response
    {
        /**
         * @var ForumForum $forum
         */
        $forum = $this->doctrine->getRepository(ForumForum::class)->find($id);
        if (is_null($forum)) {
            return $this->redirectToRoute('forum');
        }

        $this->breadcrumbHelper->addPart('general.navigation.forum.index', 'forum');
        $this->breadcrumbHelper->addPart(
            $forum->getCategory()->getName() . ' == ' . $forum->getName(),
            'forum_forum',
            ['id' => $id, 'name' => $forum->getName()],
            true
        );

        $discussions = $this->doctrine->getRepository(ForumDiscussion::class)->findByForum($forum);
        return $this->render('forum/forum.html.twig', [
            'forum' => $forum,
            'discussions' => $discussions
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param int|null $pageNumber
     * @param int|null $postId
     * @return Response|RedirectResponse
     */
    public function discussionAction(Request $request, int $id, int $pageNumber = null, int $postId = null): Response
    {
        /**
         * @var ForumDiscussion $discussion
         */
        $discussion = $this->doctrine->getRepository(ForumDiscussion::class)->find($id);
        if (is_null($discussion)) {
            return $this->redirectToRoute('forum');
        }
        if (!$this->mayView($discussion->getForum())) {
            throw new AccessDeniedException();
        }

        $this->breadcrumbHelper->addPart('general.navigation.forum.index', 'forum');
        $this->breadcrumbHelper->addPart(
            $discussion->getForum()->getCategory()->getName() . ' == ' . $discussion->getForum()->getName(),
            'forum_forum',
            ['id' => $discussion->getForum()->getId(), 'name' => $discussion->getForum()->getName()]
        );
        $this->breadcrumbHelper->addPart(
            'Discussie',
            'forum_discussion',
            ['id' => $id, 'name' => $discussion->getTitle()],
            true
        );

        $discussion->setViewed($discussion->getViewed() + 1);
        $this->doctrine->getManager()->flush();

        $numberOfPosts = $this->doctrine->getRepository(ForumDiscussion::class)->getNumberOfPosts($discussion);
        $numberOfPages = floor(($numberOfPosts - 1) / self::MAX_POSTS_PER_PAGE) + 1;
        if (is_null($pageNumber)) {
            $pageNumber = 1;
        }


//TODO postId afhandelen!!

        /**
         * @var ForumPost[] $posts
         */
        $numberOfReadPosts = 0;
        if ($this->userIsLoggedIn()) {
            if ($discussion->getForum()->getType() < 4) {
                $numberOfReadPosts = $this->doctrine->getRepository(ForumDiscussion::class)->getNumberOfReadPosts(
                    $discussion,
                    $this->getUser()
                );
                $pageNumber = floor($numberOfReadPosts / self::MAX_POSTS_PER_PAGE) + 1;
            }
            $posts = $this->doctrine->getRepository(ForumPost::class)->findBy(
                ['discussion' => $discussion],
                ['timestamp' => 'ASC'],
                self::MAX_POSTS_PER_PAGE,
                ($pageNumber - 1) * self::MAX_POSTS_PER_PAGE
            );
            $this->doctrine->getRepository(ForumDiscussion::class)->markPostsAsRead($this->getUser(), $posts);
        } else {
            $posts = $this->doctrine->getRepository(ForumPost::class)->findBy(
                ['discussion' => $discussion],
                ['timestamp' => 'ASC'],
                self::MAX_POSTS_PER_PAGE,
                ($pageNumber - 1) * self::MAX_POSTS_PER_PAGE
            );
        }

        return $this->render('forum/discussion.html.twig', [
            'userIsModerator' => in_array($this->getUser(), $discussion->getForum()->getModerators()),
            'discussion' => $discussion,
            'numberOfPages' => $numberOfPages,
            'pageNumber' => $pageNumber,
            'posts' => $posts,
            'mayPost' => $this->mayPost($discussion->getForum()),
            'numberOfReadPosts' => $numberOfReadPosts,
            'forumBanner' => $this->getForumBanner($request),
        ]);
    }

    /**
     * @param Request $request
     * @return Banner|null
     */
    private function getForumBanner(Request $request): ?Banner
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
    private function mayView(ForumForum $forum): bool
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
    private function mayPost(ForumForum $forum): bool
    {
        if ($forum->getType() === 4) {
            return false;
        }
        if (in_array($forum->getType(), [0, 1])) {
            return $this->userIsLoggedIn();
        }
        return in_array($this->getUser(), $forum->getModerators());
    }
}
