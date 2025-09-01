<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Jargon;
use App\Helpers\TemplateHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class InformationController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly TemplateHelper $templateHelper,
    ) {
    }

    public function jargonAction(): Response
    {
        return $this->templateHelper->render('information/jargon.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Jargon',
            'jargons' => $this->doctrine->getRepository(Jargon::class)->findBy([], ['term' => 'ASC']),
        ]);
    }

    public function uicAction(): Response
    {
        return $this->templateHelper->render(
            'information/uic.html.twig',
            [TemplateHelper::PARAMETER_PAGE_TITLE => 'UIC berekenen']
        );
    }
}
