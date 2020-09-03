<?php

namespace App\Controller;

use App\Entity\Help;
use App\Helpers\TemplateHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class HelpController
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
     * @param int $id
     * @return Response
     */
    public function indexAction(int $id): Response
    {
        $item = $this->doctrine->getRepository(Help::class)->find($id);
        if (is_null($item)) {
            $item = $this->doctrine->getRepository(Help::class)->find(1);
        }

        return $this->templateHelper->render('help/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Help',
            'allItems' => $this->doctrine->getRepository(Help::class)->findBy([], ['title' => 'ASC']),
            'item' => $item,
        ]);
    }
}
