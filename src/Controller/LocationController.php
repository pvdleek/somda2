<?php

namespace App\Controller;

use App\Entity\Location;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LocationController
{
    const SEARCH_METHOD_CHARACTER = 'letter';
    const SEARCH_METHOD_SINGLE = 'specifiek';
    const SEARCH_METHOD_NAME = 'naam';
    const SEARCH_METHOD_DESCRIPTION = 'omschrijving';

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
     * @IsGranted("ROLE_ABBREVIATIONS")
     * @param string|null $searchMethod
     * @param string|null $search
     * @return Response
     */
    public function indexAction(string $searchMethod = null, string $search = null): Response
    {
        switch ($searchMethod) {
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
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Verkortingen',
            'search' => $search,
            'locations' => $locations,
        ]);
    }

    /**
     * @param string $search
     * @return JsonResponse
     */
    public function jsonAction(string $search): JsonResponse
    {
        /**
         * @var Location[] $locations
         */
        $locations = $this->doctrine->getRepository(Location::class)->findByName($search);
        if (count($locations) < 1) {
            $locations = $this->doctrine->getRepository(Location::class)->findByName('%' . $search . '%');
            if (count($locations) < 1) {
                $locations = $this->doctrine->getRepository(Location::class)->findByDescription('%' . $search . '%');
            }
        }
        $locations = array_slice($locations, 0, 20);

        $json = [];
        foreach ($locations as $location) {
            $json[] = [
                'id' => $location->getId(),
                'label' => $location->name . ' - ' . $location->description,
                'description' => $location->description,
                'value' => $location->name
            ];
        }

        return new JsonResponse($json);
    }
}
