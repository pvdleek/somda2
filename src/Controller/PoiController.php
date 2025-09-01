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
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
        private readonly TemplateHelper $templateHelper,
    ) {
    }

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

    public function detailAction(int $id): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_RECENT);

        return $this->templateHelper->render('poi/details.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Spotpunten',
            'poi' => $this->doctrine->getRepository(Poi::class)->find($id),
        ]);
    }
}
