<?php

namespace App\Controller;

use App\Entity\Jargon;
use App\Helpers\TemplateHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class InformationController
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param TemplateHelper $templateHelper
     */
    public function __construct(ManagerRegistry $doctrine, TemplateHelper $templateHelper)
    {
        $this->doctrine = $doctrine;
        $this->templateHelper = $templateHelper;
    }

    /**
     * @return Response
     */
    public function jargonAction(): Response
    {
        return $this->templateHelper->render('information/jargon.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Jargon',
            'jargons' => $this->doctrine->getRepository(Jargon::class)->findAll()
        ]);
    }

    /**
     * @return Response
     */
    public function uicAction(): Response
    {
        return $this->templateHelper->render(
            'information/uic.html.twig',
            [TemplateHelper::PARAMETER_PAGE_TITLE => 'UIC berekenen']
        );
    }
}
