<?php

namespace App\Controller;

use App\Entity\UserPreference;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeLayoutController
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
     * @IsGranted("ROLE_USER")
     * @param string $layout
     * @return JsonResponse
     * @throws Exception
     */
    public function updateAction(string $layout): JsonResponse
    {
        $userPreference = $this->userHelper->getPreferenceByKey(UserPreference::KEY_HOME_LAYOUT);
        $userPreference->value = $layout;
        $this->doctrine->getManager()->flush();

        return new JsonResponse();
    }
}
