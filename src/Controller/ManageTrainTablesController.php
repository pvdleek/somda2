<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\TrainTableYear;
use App\Generics\RoleGenerics;
use App\Helpers\FlashHelper;
use App\Helpers\FormHelper;
use App\Helpers\RouteManagementHelper;
use App\Helpers\RoutesDisplayHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ManageTrainTablesController
{
    public function __construct(
        private readonly UserHelper $userHelper,
        private readonly FormHelper $formHelper,
        private readonly TemplateHelper $templateHelper,
        private readonly RoutesDisplayHelper $routesDisplayHelper,
        private readonly RouteManagementHelper $routeMgmtHelper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function manageAction(int $yearId = null, int $routeListId = null): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_TRAINTABLE_EDIT);

        $routesDisplay = $this->routesDisplayHelper->getRoutesDisplay($yearId, $routeListId);

        return $this->templateHelper->render('manageTrainTables/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer dienstregelingen',
            'trainTableYears' => $this->formHelper
                ->getDoctrine()
                ->getRepository(TrainTableYear::class)
                ->findBy([], ['startDate' => 'DESC']),
            'selectedTrainTableYear' => $routesDisplay->trainTableYear,
            'routeLists' => $routesDisplay->routeLists,
            'selectedRouteList' => $routesDisplay->selectedRouteList,
            'routes' => $routesDisplay->routes,
        ]);
    }

    public function manageRouteAction(Request $request, int $routeListId, int $routeId, int $routeNumber = null): Response|RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_TRAINTABLE_EDIT);

        $this->routeMgmtHelper->setRouteListFromId($routeListId);
        $this->routeMgmtHelper->setRouteFromId($routeId);

        if (!$this->routeMgmtHelper->setTrainTableLines($routeNumber)) {
            $this->formHelper->getFlashHelper()->add(
                FlashHelper::FLASH_TYPE_ERROR,
                'Het door jou opgegeven treinnummer ' . $routeNumber .
                ' past niet in de vastgelegde treinnummerlijst, neem contact op met het beheer'
            );

            return $this->formHelper->getRedirectHelper()->redirectToRoute('manage_train_tables_year_route_list', [
                'yearId' => $this->routeMgmtHelper->getRouteList()->trainTableYear->id,
                'routeListId' => $routeListId
            ]);
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            if ($this->routeMgmtHelper->handlePost($routeId, $request->request->all())) {
                return $this->formHelper->finishFormHandling(
                    'Trein opgeslagen',
                    'manage_train_tables_year_route_list',
                    [
                        'yearId' => $this->routeMgmtHelper->getRouteList()->trainTableYear->id,
                        'routeListId' => $this->routeMgmtHelper->getRouteList()->id,
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
            'routeList' => $this->routeMgmtHelper->getRouteList(),
            'route' => $this->routeMgmtHelper->getRoute(),
            'trainTableLines' => $this->routeMgmtHelper->getTrainTableLines(),
        ]);
    }

    public function deleteRouteAction(int $yearId, int $routeListId, int $routeId): RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_TRAINTABLE_EDIT);

        $trainTableYear = $this->formHelper->getDoctrine()->getRepository(TrainTableYear::class)->find($yearId);
        if (null === $trainTableYear) {
            throw new AccessDeniedException('This trainTableYear does not exist');
        }
        $this->routeMgmtHelper->setRouteListFromId($routeListId);
        $this->routeMgmtHelper->setRouteFromId($routeId);

        $route = $this->routeMgmtHelper->getRoute();
        $route->removeRouteList($this->routeMgmtHelper->getRouteList());
        $this->routeMgmtHelper->getRouteList()->removeRoute($route);

        foreach ($route->getTrainTables() as $trainTable) {
            if ($trainTable->trainTableYear === $trainTableYear) {
                $this->formHelper->getDoctrine()->getManager()->remove($trainTable);
            }
        }
        foreach ($route->getTrainTableFirstLasts() as $trainTableFirstLast) {
            if ($trainTableFirstLast->trainTableYear === $trainTableYear) {
                $this->formHelper->getDoctrine()->getManager()->remove($trainTableFirstLast);
            }
        }
        $this->formHelper->getDoctrine()->getManager()->flush();

        $this->formHelper->getFlashHelper()->add(
            FlashHelper::FLASH_TYPE_INFORMATION,
            'De dienstregeling is verwijderd'
        );
        return $this->formHelper->getRedirectHelper()->redirectToRoute('manage_train_tables_year_route_list', [
            'yearId' => $yearId,
            'routeListId' => $routeListId,
        ]);
    }
}
