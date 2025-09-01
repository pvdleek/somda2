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
    public function manageAction(?int $year_id = null, ?int $route_list_id = null): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_TRAINTABLE_EDIT);

        $routes_display = $this->routesDisplayHelper->getRoutesDisplay($year_id, $route_list_id);

        return $this->templateHelper->render('manageTrainTables/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer dienstregelingen',
            'train_table_years' => $this->formHelper
                ->getDoctrine()
                ->getRepository(TrainTableYear::class)
                ->findBy([], ['start_date' => 'DESC']),
            'selected_train_table_year' => $routes_display->train_table_year,
            'route_lists' => $routes_display->route_lists,
            'selected_route_list' => $routes_display->selected_route_list,
            'routes' => $routes_display->routes,
        ]);
    }

    public function manageRouteAction(Request $request, int $route_list_id, int $route_id, ?int $route_number = null): Response|RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_TRAINTABLE_EDIT);

        $this->routeMgmtHelper->setRouteListFromId($route_list_id);
        $this->routeMgmtHelper->setRouteFromId($route_id);

        if (!$this->routeMgmtHelper->setTrainTableLines($route_number)) {
            $this->formHelper->getFlashHelper()->add(
                FlashHelper::FLASH_TYPE_ERROR,
                'Het door jou opgegeven treinnummer '.$route_number.' past niet in de vastgelegde treinnummerlijst, neem contact op met het beheer'
            );

            return $this->formHelper->getRedirectHelper()->redirectToRoute('manage_train_tables_year_route_list', [
                'year_id' => $this->routeMgmtHelper->getRouteList()->train_table_year->id,
                'route_list_id' => $route_list_id
            ]);
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            if ($this->routeMgmtHelper->handlePost($route_id, $request->request->all())) {
                return $this->formHelper->finishFormHandling(
                    'Trein opgeslagen',
                    'manage_train_tables_year_route_list',
                    [
                        'year_id' => $this->routeMgmtHelper->getRouteList()->train_table_year->id,
                        'route_list_id' => $this->routeMgmtHelper->getRouteList()->id,
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

    public function deleteRouteAction(int $year_id, int $route_list_id, int $route_id): RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_TRAINTABLE_EDIT);

        $train_table_year = $this->formHelper->getDoctrine()->getRepository(TrainTableYear::class)->find($year_id);
        if (null === $train_table_year) {
            throw new AccessDeniedException('This train_table_year does not exist');
        }
        $this->routeMgmtHelper->setRouteListFromId($route_list_id);
        $this->routeMgmtHelper->setRouteFromId($route_id);

        $route = $this->routeMgmtHelper->getRoute();
        $route->removeRouteList($this->routeMgmtHelper->getRouteList());
        $this->routeMgmtHelper->getRouteList()->removeRoute($route);

        foreach ($route->getTrainTables() as $trainTable) {
            if ($trainTable->train_table_year === $train_table_year) {
                $this->formHelper->getDoctrine()->getManager()->remove($trainTable);
            }
        }
        foreach ($route->getTrainTableFirstLasts() as $train_table_first_last) {
            if ($train_table_first_last->train_table_year === $train_table_year) {
                $this->formHelper->getDoctrine()->getManager()->remove($train_table_first_last);
            }
        }
        $this->formHelper->getDoctrine()->getManager()->flush();

        $this->formHelper->getFlashHelper()->add(FlashHelper::FLASH_TYPE_INFORMATION, 'De dienstregeling is verwijderd');
        return $this->formHelper->getRedirectHelper()->redirectToRoute('manage_train_tables_year_route_list', [
            'year_id' => $year_id,
            'route_list_id' => $route_list_id,
        ]);
    }
}
