<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ForumFavorite;
use App\Entity\ForumPostFavorite;
use App\Generics\RoleGenerics;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Repository\ForumDiscussionRepository;
use App\Repository\ForumPostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ForumPostFavoriteController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $user_helper,
        private readonly TemplateHelper $template_helper,
        private readonly ForumDiscussionRepository $forum_discussion_repository,
        private readonly ForumPostRepository $forum_post_repository,
    ) {
    }

    public function indexAction(): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        return $this->template_helper->render('forum/favorites.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum favorieten',
            'favorites' => $this->forum_discussion_repository->findByFavorites($this->user_helper->getUser()),
            'postFavorites' => $this->forum_post_repository->findByFavorites($this->user_helper->getUser()),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function toggleAction(int $id, int $alerting): JsonResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        if (null === ($favorite = $this->getFavorite($id))) {
            return new JsonResponse();
        }

        $favorite->alerting = $alerting;
        $this->doctrine->getManager()->flush();

        return new JsonResponse();
    }

    /**
     * @throws \Exception
     */
    public function addAction(int $id): JsonResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $discussion = $this->forum_discussion_repository->find($id);
        if (null === $discussion) {
            return new JsonResponse();
        }

        $favorite = new ForumFavorite();
        $favorite->discussion = $discussion;
        $favorite->user = $this->user_helper->getUser();

        $this->doctrine->getManager()->persist($favorite);
        $this->doctrine->getManager()->flush();

        return new JsonResponse();
    }

    /**
     * @throws \Exception
     */
    public function removeAction(int $id): JsonResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        if (null === ($favorite = $this->getFavorite($id))) {
            return new JsonResponse();
        }

        $this->doctrine->getManager()->remove($favorite);
        $this->doctrine->getManager()->flush();

        return new JsonResponse();
    }

    /**
     * @throws \Exception
     */
    public function addPostAction(int $id): JsonResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $post = $this->forum_post_repository->find($id);
        if (null === $post) {
            return new JsonResponse();
        }

        $favorite = new ForumPostFavorite();
        $favorite->post = $post;
        $favorite->user = $this->user_helper->getUser();

        $this->doctrine->getManager()->persist($favorite);
        $this->doctrine->getManager()->flush();

        return new JsonResponse();
    }

    /**
     * @throws \Exception
     */
    public function removePostAction(int $id): JsonResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $post = $this->forum_post_repository->find($id);
        if (null === $post) {
            return new JsonResponse();
        }

        /** @var ForumPostFavorite|null $favorite */
        $favorite = $this->doctrine->getRepository(ForumPostFavorite::class)->findOneBy(
            ['post' => $post, 'user' => $this->user_helper->getUser()]
        );
        if (null === $favorite) {
            return new JsonResponse();
        }

        $this->doctrine->getManager()->remove($favorite);
        $this->doctrine->getManager()->flush();

        return new JsonResponse();
    }

    private function getFavorite(int $id): ?ForumFavorite
    {
        $discussion = $this->forum_discussion_repository->find($id);
        if (null === $discussion) {
            return null;
        }

        /** @var ForumFavorite|null $favorite */
        $favorite = $this->doctrine->getRepository(ForumFavorite::class)->findOneBy(
            ['discussion' => $discussion, 'user' => $this->user_helper->getUser()]
        );
        if (null === $favorite) {
            return null;
        }

        return $favorite;
    }
}
