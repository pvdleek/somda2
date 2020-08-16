<?php

namespace App\Controller;

use App\Entity\ForumPost;
use App\Generics\RoleGenerics;
use App\Helpers\UserHelper;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ForumPostWikiController
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     */
    public function __construct(ManagerRegistry $doctrine, UserHelper $userHelper)
    {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
    }

    /**
     * @param int $id
     * @param string $operation
     * @return JsonResponse
     */
    public function checkAction(int $id, string $operation)
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_WIKI);

        /**
         * @var ForumPost $post
         */
        $post = $this->doctrine->getRepository(ForumPost::class)->find($id);
        if (is_null($post)) {
            throw new AccessDeniedException('This post does not exist');
        }

        $post->wikiCheck = $operation === 'ok' ? ForumPost::WIKI_CHECK_OK : ForumPost::WIKI_CHECK_N_A;
        $post->wikiChecker = $this->userHelper->getUser();
        $this->doctrine->getManager()->flush();

        return new JsonResponse();
    }
}
