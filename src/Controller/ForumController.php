<?php

namespace App\Controller;

use App\Entity\ForumCategory;
use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use Symfony\Component\HttpFoundation\Response;

class ForumController extends BaseController
{
    /**
     * @return Response
     */
    public function indexAction() : Response
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
     * @return Response
     */
    public function forumAction(int $id) : Response
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
}
