<?php

namespace App\Controller;

use App\Entity\Jargon;
use App\Entity\Location;
use Symfony\Component\HttpFoundation\Response;

class InformationController extends BaseController
{
    const SEARCH_METHOD_CHARACTER = 'letter';
    const SEARCH_METHOD_SINGLE = 'specifiek';
    const SEARCH_METHOD_NAME = 'naam';
    const SEARCH_METHOD_DESCRIPTION = 'omschrijving';

    /**
     * @param string|null $searchMethod
     * @param string|null $search
     * @return Response
     */
    public function locationsAction(string $searchMethod = null, string $search = null): Response
    {
        $this->breadcrumbHelper->addPart('general.navigation.information.home', 'information_home');
        $this->breadcrumbHelper->addPart('general.navigation.information.locations', 'location', [], true);

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

        return $this->render('information/locations.html.twig', [
            'search' => $search,
            'locations' => $locations,
        ]);
    }

    /**
     * @return Response
     */
    public function jargonAction(): Response
    {
        $this->breadcrumbHelper->addPart('general.navigation.information.home', 'information_home');
        $this->breadcrumbHelper->addPart('general.navigation.information.jargon', 'jargon', [], true);

        return $this->render('information/jargon.html.twig', [
            'jargons' => $this->doctrine->getRepository(Jargon::class)->findAll()
        ]);
    }

    /**
     * @return Response
     */
    public function uicAction(): Response
    {
        $this->breadcrumbHelper->addPart('general.navigation.information.home', 'information_home');
        $this->breadcrumbHelper->addPart('general.navigation.information.uic', 'uic', [], true);

        return $this->render('information/uic.html.twig');
    }
}
