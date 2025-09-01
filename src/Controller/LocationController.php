<?php

declare(strict_types=1);

namespace App\Controller;

use App\Generics\RoleGenerics;
use App\Entity\Location;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Repository\LocationRepository;
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
        private readonly UserHelper $user_helper,
        private readonly TemplateHelper $template_helper,
        private readonly LocationRepository $location_repository,
    ) {
    }

    public function indexAction(?string $search_method = null, ?string $search = null): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ABBREVIATIONS);

        switch ($search_method) {
            case self::SEARCH_METHOD_CHARACTER:
                if ($search === '*') {
                    $search = '%';
                }
                $locations = $this->location_repository->findByName($search . '%');
                break;
            case self::SEARCH_METHOD_SINGLE:
                $locations = $this->location_repository->findByName($search);
                break;
            case self::SEARCH_METHOD_NAME:
                $locations = $this->location_repository->findByName('%' . $search . '%');
                break;
            case self::SEARCH_METHOD_DESCRIPTION:
                $locations = $this->location_repository->findByDescription('%' . $search . '%');
                break;
            default:
                $locations = $this->location_repository->findAll();
                break;
        }

        return $this->template_helper->render('information/locations.html.twig', [
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
        $locations = $this->location_repository->findByName($search);
        if (\count($locations) < 1) {
            $locations = $this->location_repository->findByName('%' . $search . '%');
            if (\count($locations) < 1) {
                $locations = $this->location_repository->findByDescription('%' . $search . '%');
            }
        }
        $locations = array_slice($locations, 0, 20);

        $json = [];
        foreach ($locations as $location) {
            $json[] = [
                'id' => $location->id,
                'label' => $location->name.' - '.$location->description,
                'description' => $location->description,
                'value' => $location->name
            ];
        }

        return new JsonResponse($json);
    }
}
