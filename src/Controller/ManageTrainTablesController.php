<?php

namespace App\Controller;

use App\Entity\TrainTableYear;
use App\Helpers\FlashHelper;
use App\Helpers\FormHelper;
use App\Helpers\RedirectHelper;
use App\Helpers\RouteManagementHelper;
use App\Helpers\RoutesDisplayHelper;
use App\Helpers\TemplateHelper;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManageTrainTablesController
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @var RedirectHelper
     */
    private RedirectHelper $redirectHelper;

    /**
     * @var RoutesDisplayHelper
     */
    private RoutesDisplayHelper $routesDisplayHelper;

    /**
     * @var RouteManagementHelper
     */
    private RouteManagementHelper $routeManagementHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param FormHelper $formHelper
     * @param TemplateHelper $templateHelper
     * @param RedirectHelper $redirectHelper
     * @param RoutesDisplayHelper $routesDisplayHelper
     * @param RouteManagementHelper $routeManagementHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        FormHelper $formHelper,
        TemplateHelper $templateHelper,
        RedirectHelper $redirectHelper,
        RoutesDisplayHelper $routesDisplayHelper,
        RouteManagementHelper $routeManagementHelper
    ) {
        $this->doctrine = $doctrine;
        $this->formHelper = $formHelper;
        $this->templateHelper = $templateHelper;
        $this->redirectHelper = $redirectHelper;
        $this->routesDisplayHelper = $routesDisplayHelper;
        $this->routeManagementHelper = $routeManagementHelper;
    }

    /**
     * @IsGranted("ROLE_ADMIN_TRAINTABLE_EDIT")
     * @param int|null $yearId
     * @param int|null $routeListId
     * @return Response
     * @throws Exception
     */
    public function manageAction(int $yearId = null, int $routeListId = null): Response
    {
        $routesDisplay = $this->routesDisplayHelper->getRoutesDisplay($yearId, $routeListId);

        return $this->templateHelper->render('manageTrainTables/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer dienstregelingen',
            'trainTableYears' => $this->doctrine
                ->getRepository(TrainTableYear::class)
                ->findBy([], ['startDate' => 'DESC']),
            'selectedTrainTableYear' => $routesDisplay->trainTableYear,
            'routeLists' => $routesDisplay->routeLists,
            'selectedRouteList' => $routesDisplay->selectedRouteList,
            'routes' => $routesDisplay->routes,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN_TRAINTABLE_EDIT")
     * @param Request $request
     * @param int $routeListId
     * @param int $routeId
     * @param int|null $routeNumber
     * @return RedirectResponse|Response
     */
    public function manageRouteAction(Request $request, int $routeListId, int $routeId, int $routeNumber = null)
    {
        $this->routeManagementHelper->setRouteListFromId($routeListId);
        $this->routeManagementHelper->setRouteFromId($routeId);

        if (!$this->routeManagementHelper->setTrainTableLines($routeNumber)) {
            $this->formHelper->getFlashHelper()->add(
                FlashHelper::FLASH_TYPE_ERROR,
                'Het door jou opgegeven treinnummer ' . $routeNumber .
                ' past niet in de vastgelegde treinnummerlijst, neem contact op met het beheer'
            );

            return $this->redirectHelper->redirectToRoute('manage_train_tables_year_route_list', [
                'yearId' => $this->routeManagementHelper->getRouteList()->trainTableYear->getId(),
                'routeListId' => $routeListId
            ]);
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            if ($this->routeManagementHelper->handlePost($routeId, $request->request->all())) {
                return $this->formHelper->finishFormHandling(
                    'Trein opgeslagen',
                    'manage_train_tables_year_route_list',
                    [
                        'yearId' => $this->routeManagementHelper->getRouteList()->trainTableYear->getId(),
                        'routeListId' => $this->routeManagementHelper->getRouteList()->getId(),
                    ]
                );
            }

            $this->formHelper->getFlashHelper()->add(
                FlashHelper::FLASH_TYPE_ERROR,
                '1 Of meer locaties konden niet worden gevonden, corrigeer aub en sla opnieuw op'
            );
        }

        return $this->templateHelper->render('manageTrainTables/item.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer dienstregeling',
            'routeList' => $this->routeManagementHelper->getRouteList(),
            'route' => $this->routeManagementHelper->getRoute(),
            'trainTableLines' => $this->routeManagementHelper->getTrainTableLines(),
        ]);
    }
}
