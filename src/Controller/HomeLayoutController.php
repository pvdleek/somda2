<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserPreference;
use App\Generics\RoleGenerics;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeLayoutController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $user_helper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function updateAction(string $layout): JsonResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $userPreference = $this->user_helper->getPreferenceByKey(UserPreference::KEY_HOME_LAYOUT);
        $userPreference->value = $layout;
        $this->doctrine->getManager()->flush();

        return new JsonResponse();
    }
}
