<?php

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
        private readonly UserHelper $userHelper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function updateAction(string $layout): JsonResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $userPreference = $this->userHelper->getPreferenceByKey(UserPreference::KEY_HOME_LAYOUT);
        $userPreference->value = $layout;
        $this->doctrine->getManager()->flush();

        return new JsonResponse();
    }
}
