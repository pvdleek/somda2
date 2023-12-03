<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\ForumDiscussion;
use App\Entity\ForumFavorite;
use App\Entity\ForumPost;
use App\Entity\ForumPostFavorite;
use App\Generics\RoleGenerics;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ForumPostFavoriteController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
        private readonly TemplateHelper $templateHelper,
    ) {
    }

    public function indexAction(): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        return $this->templateHelper->render('forum/favorites.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum favorieten',
            'favorites' => $this->doctrine->getRepository(ForumDiscussion::class)->findByFavorites(
                $this->userHelper->getUser()
            ),
            'postFavorites' => $this->doctrine->getRepository(ForumPost::class)->findByFavorites(
                $this->userHelper->getUser()
            ),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function toggleAction(int $id, int $alerting): JsonResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

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
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $discussion = $this->doctrine->getRepository(ForumDiscussion::class)->find($id);
        if (null === $discussion) {
            return new JsonResponse();
        }

        $favorite = new ForumFavorite();
        $favorite->discussion = $discussion;
        $favorite->user = $this->userHelper->getUser();

        $this->doctrine->getManager()->persist($favorite);
        $this->doctrine->getManager()->flush();

        return new JsonResponse();
    }

    /**
     * @throws \Exception
     */
    public function removeAction(int $id): JsonResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

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
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $post = $this->doctrine->getRepository(ForumPost::class)->find($id);
        if (null === $post) {
            return new JsonResponse();
        }

        $favorite = new ForumPostFavorite();
        $favorite->post = $post;
        $favorite->user = $this->userHelper->getUser();

        $this->doctrine->getManager()->persist($favorite);
        $this->doctrine->getManager()->flush();

        return new JsonResponse();
    }

    /**
     * @throws \Exception
     */
    public function removePostAction(int $id): JsonResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $post = $this->doctrine->getRepository(ForumPost::class)->find($id);
        if (null === $post) {
            return new JsonResponse();
        }

        /**
         * @var ForumPostFavorite $favorite
         */
        $favorite = $this->doctrine->getRepository(ForumPostFavorite::class)->findOneBy(
            ['post' => $post, 'user' => $this->userHelper->getUser()]
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
        $discussion = $this->doctrine->getRepository(ForumDiscussion::class)->find($id);
        if (null === $discussion) {
            return null;
        }

        /**
         * @var ForumFavorite $favorite
         */
        $favorite = $this->doctrine->getRepository(ForumFavorite::class)->findOneBy(
            ['discussion' => $discussion, 'user' => $this->userHelper->getUser()]
        );
        if (null === $favorite) {
            return null;
        }

        return $favorite;
    }
}
