<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\ForumPost;
use App\Generics\RoleGenerics;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ForumPostWikiController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
    ) {
    }

    public function checkAction(int $id, string $operation): JsonResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_WIKI);

        /**
         * @var ForumPost $post
         */
        $post = $this->doctrine->getRepository(ForumPost::class)->find($id);
        if (null === $post) {
            throw new AccessDeniedException('This post does not exist');
        }

        $post->wikiCheck = $operation === 'ok' ? ForumPost::WIKI_CHECK_OK : ForumPost::WIKI_CHECK_N_A;
        $post->wikiChecker = $this->userHelper->getUser();
        $this->doctrine->getManager()->flush();

        return new JsonResponse();
    }
}
