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
        private readonly UserHelper $user_helper,
        private readonly TemplateHelper $template_helper,
    ) {
    }

    public function indexAction(): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_RECENT);

        $pois = $this->doctrine
            ->getRepository(Poi::class)
            ->findBy([], ['category' => 'asc', 'name' => 'asc']);

        return $this->template_helper->render('poi/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Spotpunten',
            'pois' => $pois,
        ]);
    }

    public function detailAction(int $id): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_RECENT);

        return $this->template_helper->render('poi/details.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Spotpunten',
            'poi' => $this->doctrine->getRepository(Poi::class)->find($id),
        ]);
    }
}
