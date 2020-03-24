<?php

namespace App\Controller;

use App\Entity\Banner;
use App\Entity\BannerView;
use App\Entity\ForumCategory;
use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Entity\ForumPost;
use App\Entity\RailNews;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ForumController extends BaseController
{
    private const MAX_POSTS_PER_PAGE = 100;

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
        //TODO
        $highlight = '';
        $forum_jump = '';




        /**
         * @var ForumDiscussion $discussion
         */
        $discussion = $this->doctrine->getRepository(ForumDiscussion::class)->find($id);
        if (is_null($discussion)) {
            return $this->redirectToRoute('forum');
        }
        if (!$this->mayView($discussion->getForum()->getId(), $discussion->getForum()->getType())) {
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

        // Check if there is an active banner for the forum
        $banners = $this->doctrine->getRepository(Banner::class)->findBy(
            ['location' => Banner::LOCATION_FORUM, 'active' => true]
        );
        if (count($banners) > 0) {
            $forumBanner = $banners[rand(0, count($banners) - 1)];

            // Create a view for this banner
            $bannerView = new BannerView();
            $bannerView
                ->setBanner($forumBanner)
                ->setTimestamp(time())
                ->setIp(inet_pton($request->getClientIp()));
            $this->doctrine->getManager()->persist($bannerView);
            $this->doctrine->getManager()->flush();
        } else {
            $forumBanner = null;
        }

        $discussion->setViewed($discussion->getViewed() + 1);
        $this->doctrine->getManager()->flush();

        $numberOfPosts = $this->doctrine->getRepository(ForumDiscussion::class)->getNumberOfPosts($discussion);
        $numberOfPages = floor(($numberOfPosts - 1) / self::MAX_POSTS_PER_PAGE) + 1;


// postId afhandelen!!


        $unreadPostId = 0;
        if ($this->userIsLoggedIn()) {
//             Uitzoeken naar welke pagina we gaan
//             Posts op die pagina als gelezen markeren
            if ($discussion->getForum()->getType() < 4) {
                $unreadPostId = $this->doctrine->getRepository(ForumDiscussion::class)->getUnreadPostId($discussion, $this->getUser());
            }
        } else {
            if (is_null($pageNumber)) {
                $pageNumber = 1;
            }
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
            'mayPost' => $this->mayPost($discussion->getForum()->getId(), $discussion->getForum()->getType()),
            'unreadPostId' => $unreadPostId,
            'forumBanner' => $forumBanner,
        ]);


        if ($discussion->getForum()->getType() < 4 && $this->userIsLoggedIn()) {
            $ongelezen_postid = $this->doctrine->getRepository(ForumDiscussion::class)->getUnreadPostId($discussion, $this->getUser());
        } else {
            $ongelezen_postid = 0;
        }

        // Zoek het huidige maximale postid
        $max_postid = $this->doctrine->getRepository(ForumDiscussion::class)->getMaxPostId($discussion);

        // Bepaal naar welke pagina we gaan, als er meerdere zijn
        $aantal_posts = count($discussion->getPosts());

        // Sla het aantal pagina's op wat we moeten weergeven
        $aantal_paginas = floor(($aantal_posts - 1) / self::MAX_POSTS_PER_PAGE) + 1;
        if ($aantal_posts > self::MAX_POSTS_PER_PAGE) {
            // Het aantal berichten is meer dan per pagina is toegestaan
            if ($page_start > 0) {
                // Er is een pagina opgegeven om naar te bladeren, dus negeren we wat er nieuw of oud is en gaan we naar die pagina
                $pagina_todo = $page_start;
            } else {
                if (strlen($highlight) > 0) {
                    // Er is een zoekopdracht uitgevoerd, ga naar de pagina waar dit woord voor komt
//                        $query = 'SELECT `p`.`postid`
//                            FROM `somda_forum_posts` `p`
//                            JOIN `somda_forum_posts_text` `t` ON `t`.`postid` = `p`.`postid`
//                            WHERE `p`.`discussionid` = ' . $discussion->getId() . ' AND `t`.`text` LIKE \'%' . $highlight . '%\'
//                            ORDER BY `p`.`postid` ' . ($params['forum_new_old'] == 1 ? 'DESC' : 'ASC') . '
//                            LIMIT 1';
//                        $dbset_highlight_post = $db->query($query);
//                        if ($db->numRows($dbset_highlight_post) > 0) {
//                            list($highlight_postid) = $db->fetchRow($dbset_highlight_post);
//                            $forum_jump = 'p' . $highlight_postid;
//                        }
                }

                if (strtolower(substr($forum_jump, 0, 1)) == 'p') {
                    // Jumpen naar een specifieke post
                    $post_zoeken = substr($forum_jump, 1);
                } else {
                    // Geen pagina opgegeven, dus naar de nieuwste post
                    $post_zoeken = $ongelezen_postid;
                }

                // Zoek uit op welke pagina de gezochte post staat
                if ($params['forum_new_old']) {
                    $huidige_pagina = $aantal_paginas;
                    $post_teller = $aantal_posts;
                } else {
                    $huidige_pagina = 1;
                    $post_teller = 1;
                }

                foreach ($discussion->getPosts() as $post) {
                    // Controleer of we een nieuwe pagina moeten beginnen
                    if ($params['forum_new_old']) {
                        if ($post_teller < $aantal_posts && floor(($post_teller - 1) / self::MAX_POSTS_PER_PAGE) == (($post_teller - 1) / self::MAX_POSTS_PER_PAGE)) {
                            --$huidige_pagina;
                        }
                    } else {
                        if ($post_teller > 1 && floor(($post_teller - 1) / self::MAX_POSTS_PER_PAGE) == (($post_teller - 1) / self::MAX_POSTS_PER_PAGE)) {
                            ++$huidige_pagina;
                        }
                    }

                    // Controleer of we al bij de nieuwste post zijn
                    if ($post->getId() === $post_zoeken) {
                        break;
                    }

                    if ($params['forum_new_old'] == 1) {
                        --$post_teller;
                    } else {
                        ++$post_teller;
                    }
                }
                $pagina_todo = $huidige_pagina;
            }
        } else {
            // Het aantal berichten is minder dan per pagina is toegestaan, dus gewoon pagina 1 weergeven
            $pagina_todo = 1;
        }

        if ($pagina_todo < 1 || $pagina_todo > $aantal_paginas) {
            if ($params['forum_new_old'] == 1) {
                $pagina_todo = 1;
            } else {
                $pagina_todo = $aantal_paginas;
            }
        }

        return $this->render('forum/discussion.html.twig', [
            'userIsModerator' => in_array($this->getUser(), $discussion->getForum()->getModerators()),
            'discussion' => $discussion,
            'mayPost' => $this->mayPost($discussion->getForum()->getId(), $discussion->getForum()->getType()),
            'pagina_todo' => $pagina_todo,
            'forumBanner' => $forumBanner,
        ]);
    }

    private function mayView($forum_id, $forum_type = -1) {
        if ($forum_id < 1) { return false; }

        if ($forum_type < 0) {
            $query = 'select type from '.DB_PREFIX.'_forum_forums where forumid='.$forum_id;
            $dbset_forum_data = $db->query($query);
            if ($db->numRows($dbset_forum_data) > 0 && list($forum_type) = $db->fetchRow($dbset_forum_data)) {
                // Forum-type opgehaald
                $db->freeResult($dbset_forum_data);
            } else {
                $db->freeResult($dbset_forum_data);
                return false;
            }
        }
        switch ($forum_type) {
            case 0:		// Public
                return true;
                break;
            case 1:		// Logged in only
            case 4:		// Archive
            case 2:		// Only mods may post
                return $this->userIsLoggedIn();
            case 3:		// Only mods may view
            default:
                $query = 'select uid
                from '.DB_PREFIX.'_forum_mods
                where forumid='.$forum_id.' and uid='.$session->uid;
                $dbset_auth_data = $db->query($query);
                $mayView = ($db->numRows($dbset_auth_data) > 0);
                $db->freeResult($dbset_auth_data);
                return ($mayView);
        }

        return false;
    }

    private function mayPost($forum_id, $forum_type =- 1) {
        if ($forum_type < 0) {
            $query = 'select type from '.DB_PREFIX.'_forum_forums where forumid='.$forum_id;
            $dbset_forum_data = $db->query($query);
            if (list($forum_type) = $db->fetchRow($dbset_forum_data)) {
                // Forum-type opgehaald
                $db->freeResult($dbset_forum_data);
            } else {
                $db->freeResult($dbset_forum_data);
                return false;
            }
        }
        switch ($forum_type) {
            case 0:		// Public
            case 1:		// Logged in only
                return $this->userIsLoggedIn();
            case 4:		// Archive
                return false;
            case 2:		// Only mods may post
            case 3:		// Only mods may view
            default:
                $query = 'select uid
                from '.DB_PREFIX.'_forum_mods
                where forumid='.$forum_id.' and uid='.$session->uid;
                $dbset_auth_data = $db->query($query);
                $mayPost = ($db->numRows($dbset_auth_data) > 0);
                $db->freeResult($dbset_auth_data);
                return ($mayPost);
        }

        return false;
    }
}
