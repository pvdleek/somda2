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
        private readonly UserHelper $user_helper,
    ) {
    }

    public function checkAction(int $id, string $operation): JsonResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_WIKI);

        /** @var ForumPost|null $post */
        $post = $this->doctrine->getRepository(ForumPost::class)->find($id);
        if (null === $post) {
            throw new AccessDeniedException('This post does not exist');
        }

        $post->wiki_check = 'ok' === $operation ? ForumPost::WIKI_CHECK_OK : ForumPost::WIKI_CHECK_N_A;
        $post->wiki_checker = $this->user_helper->getUser();
        $this->doctrine->getManager()->flush();

        return new JsonResponse();
    }
}
