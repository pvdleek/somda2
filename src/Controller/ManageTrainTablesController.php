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
        private readonly FormHelper $form_helper,
        private readonly RoutesDisplayHelper $routes_display_helper,
        private readonly RouteManagementHelper $route_management_helper,
        private readonly TemplateHelper $template_helper,
        private readonly UserHelper $user_helper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function manageAction(?int $year_id = null, ?int $route_list_id = null): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_TRAINTABLE_EDIT);

        $routes_display = $this->routes_display_helper->getRoutesDisplay($year_id, $route_list_id);

        return $this->template_helper->render('manageTrainTables/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer dienstregelingen',
            'train_table_years' => $this->form_helper
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
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_TRAINTABLE_EDIT);

        $this->route_management_helper->setRouteListFromId($route_list_id);
        $this->route_management_helper->setRouteFromId($route_id);

        if (!$this->route_management_helper->setTrainTableLines($route_number)) {
            $this->form_helper->getFlashHelper()->add(
                FlashHelper::FLASH_TYPE_ERROR,
                'Het door jou opgegeven treinnummer '.$route_number.' past niet in de vastgelegde treinnummerlijst, neem contact op met het beheer'
            );

            return $this->form_helper->getRedirectHelper()->redirectToRoute('manage_train_tables_year_route_list', [
                'year_id' => $this->route_management_helper->getRouteList()->train_table_year->id,
                'route_list_id' => $route_list_id
            ]);
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            if ($this->route_management_helper->handlePost($route_id, $request->request->all())) {
                return $this->form_helper->finishFormHandling(
                    'Trein opgeslagen',
                    'manage_train_tables_year_route_list',
                    [
                        'year_id' => $this->route_management_helper->getRouteList()->train_table_year->id,
                        'route_list_id' => $this->route_management_helper->getRouteList()->id,
                    ]
                );
            }

            $this->form_helper->getFlashHelper()->add(
                FlashHelper::FLASH_TYPE_ERROR,
                '1 Of meer locaties konden niet worden gevonden, corrigeer aub en sla opnieuw op'
            );
        }

        return $this->template_helper->render('manageTrainTables/item.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer dienstregeling',
            'routeList' => $this->route_management_helper->getRouteList(),
            'route' => $this->route_management_helper->getRoute(),
            'trainTableLines' => $this->route_management_helper->getTrainTableLines(),
        ]);
    }

    public function deleteRouteAction(int $year_id, int $route_list_id, int $route_id): RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_TRAINTABLE_EDIT);

        $train_table_year = $this->form_helper->getDoctrine()->getRepository(TrainTableYear::class)->find($year_id);
        if (null === $train_table_year) {
            throw new AccessDeniedException('This train_table_year does not exist');
        }
        $this->route_management_helper->setRouteListFromId($route_list_id);
        $this->route_management_helper->setRouteFromId($route_id);

        $route = $this->route_management_helper->getRoute();
        $route->removeRouteList($this->route_management_helper->getRouteList());
        $this->route_management_helper->getRouteList()->removeRoute($route);

        foreach ($route->getTrainTables() as $train_table) {
            if ($train_table->train_table_year === $train_table_year) {
                $this->form_helper->getDoctrine()->getManager()->remove($train_table);
            }
        }
        foreach ($route->getTrainTableFirstLasts() as $train_table_first_last) {
            if ($train_table_first_last->train_table_year === $train_table_year) {
                $this->form_helper->getDoctrine()->getManager()->remove($train_table_first_last);
            }
        }
        $this->form_helper->getDoctrine()->getManager()->flush();

        $this->form_helper->getFlashHelper()->add(FlashHelper::FLASH_TYPE_INFORMATION, 'De dienstregeling is verwijderd');
        return $this->form_helper->getRedirectHelper()->redirectToRoute('manage_train_tables_year_route_list', [
            'year_id' => $year_id,
            'route_list_id' => $route_list_id,
        ]);
    }
}
