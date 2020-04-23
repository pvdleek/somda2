<?php

namespace App\Controller;

use App\Entity\Jargon;
use App\Entity\Location;
use App\Helpers\TemplateHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class InformationController
{
    const SEARCH_METHOD_CHARACTER = 'letter';
    const SEARCH_METHOD_SINGLE = 'specifiek';
    const SEARCH_METHOD_NAME = 'naam';
    const SEARCH_METHOD_DESCRIPTION = 'omschrijving';

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var TemplateHelper
     */
    private $templateHelper;

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
     * @param string|null $searchMethod
     * @param string|null $search
     * @return Response
     */
    public function locationsAction(string $searchMethod = null, string $search = null): Response
    {
        switch($searchMethod) {
            case self::SEARCH_METHOD_CHARACTER:
                $locations = $this->doctrine->getRepository(Location::class)->findByName($search . '%');
                break;
            case self::SEARCH_METHOD_SINGLE:
                $locations = $this->doctrine->getRepository(Location::class)->findByName($search);
                break;
            case self::SEARCH_METHOD_NAME:
                $locations = $this->doctrine->getRepository(Location::class)->findByName('%' . $search . '%');
                break;
            case self::SEARCH_METHOD_DESCRIPTION:
                $locations = $this->doctrine->getRepository(Location::class)->findByDescription('%' . $search . '%');
                break;
            default:
                $locations = $this->doctrine->getRepository(Location::class)->findAll();
                break;
        }

        return $this->templateHelper->render('information/locations.html.twig', [
            'search' => $search,
            'locations' => $locations,
        ]);
    }

    /**
     * @return Response
     */
    public function jargonAction(): Response
    {
        return $this->templateHelper->render('information/jargon.html.twig', [
            'jargons' => $this->doctrine->getRepository(Jargon::class)->findAll()
        ]);
    }

    /**
     * @return Response
     */
    public function uicAction(): Response
    {
        return $this->templateHelper->render('information/uic.html.twig');
    }
}
