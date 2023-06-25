<?php

namespace App\Controller;

use App\Generics\RoleGenerics;
use App\Entity\Location;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LocationController
{
    public const SEARCH_METHOD_CHARACTER = 'letter';
    public const SEARCH_METHOD_SINGLE = 'specifiek';
    public const SEARCH_METHOD_NAME = 'naam';
    public const SEARCH_METHOD_DESCRIPTION = 'omschrijving';

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
        private readonly TemplateHelper $templateHelper,
    ) {
    }

    public function indexAction(string $searchMethod = null, string $search = null): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ABBREVIATIONS);

        switch ($searchMethod) {
            case self::SEARCH_METHOD_CHARACTER:
                if ($search === '*') {
                    $search = '%';
                }
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

    public function jsonAction(string $search): JsonResponse
    {
        /**
         * @var Location[] $locations
         */
        $locations = $this->doctrine->getRepository(Location::class)->findByName($search);
        if (\count($locations) < 1) {
            $locations = $this->doctrine->getRepository(Location::class)->findByName('%' . $search . '%');
            if (\count($locations) < 1) {
                $locations = $this->doctrine->getRepository(Location::class)->findByDescription('%' . $search . '%');
            }
        }
        $locations = array_slice($locations, 0, 20);

        $json = [];
        foreach ($locations as $location) {
            $json[] = [
                'id' => $location->id,
                'label' => $location->name . ' - ' . $location->description,
                'description' => $location->description,
                'value' => $location->name
            ];
        }

        return new JsonResponse($json);
    }
}
