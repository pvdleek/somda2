<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Poi;
use App\Generics\RoleGenerics;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class PoiController
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
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     * @param TemplateHelper $templateHelper
     */
    public function __construct(ManagerRegistry $doctrine, UserHelper $userHelper, TemplateHelper $templateHelper)
    {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
        $this->templateHelper = $templateHelper;
    }

    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_RECENT);

        $pois = $this->doctrine
            ->getRepository(Poi::class)
            ->findBy([], ['category' => 'asc', 'name' => 'asc']);

        return $this->templateHelper->render('poi/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Spotpunten',
            'pois' => $pois,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function detailAction(int $id): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_RECENT);

        return $this->templateHelper->render('poi/details.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Spotpunten',
            'poi' => $this->doctrine->getRepository(Poi::class)->find($id),
        ]);
    }
}
