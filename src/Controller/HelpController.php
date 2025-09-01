<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Help;
use App\Helpers\TemplateHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class HelpController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly TemplateHelper $templateHelper,
    ) {
    }

    public function indexAction(int $id): Response
    {
        $item = $this->doctrine->getRepository(Help::class)->find($id);
        if (null === $item) {
            $item = $this->doctrine->getRepository(Help::class)->find(1);
        }

        return $this->templateHelper->render('help/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Help',
            'allItems' => $this->doctrine->getRepository(Help::class)->findBy([], ['title' => 'ASC']),
            'item' => $item,
        ]);
    }
}
