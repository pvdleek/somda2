<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Characteristic;
use App\Entity\RouteList;
use App\Entity\Transporter;
use App\Form\RouteList as RouteListForm;
use App\Generics\RoleGenerics;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Repository\TrainTableYearRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ManageRouteListsController
{
    public function __construct(
        private readonly UserHelper $user_helper,
        private readonly FormHelper $form_helper,
        private readonly TemplateHelper $template_helper,
        private readonly TrainTableYearRepository $train_table_year_repository,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function routeListsAction(?int $id = null): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_ROUTE_NUMBER_LIST);

        if (null === $id) {
            $train_table_year = $this->train_table_year_repository->findTrainTableYearByDate(new \DateTime());
            $route_lists = [];
        } else {
            $train_table_year = $this->train_table_year_repository->find($id);
            if (null === $train_table_year) {
                throw new AccessDeniedException('This train_table_year does not exist');
            }

            $route_lists = $this->form_helper->getDoctrine()->getRepository(RouteList::class)->findBy(
                ['train_table_year' => $train_table_year],
                ['first_number' => 'ASC']
            );
        }

        return $this->template_helper->render('manageRouteLists/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer treinnummerlijst',
            'train_table_years' => $this->train_table_year_repository->findBy([], ['start_date' => 'DESC']),
            'train_table_year' => $train_table_year,
            'route_lists' => $route_lists,
        ]);
    }

    public function routeListAction(Request $request, int $year_id, int $id): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_ROUTE_NUMBER_LIST);

        $route_list = $this->getOrCreateRouteList($year_id, $id);
        $form = $this->form_helper->getFactory()->create(RouteListForm::class, $route_list);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($route_list->id > 0) {
                $message = 'Treinnummer bijgewerkt';
            } else {
                // New routeList
                $message = 'Treinnummer toegevoegd';
                $this->form_helper->getDoctrine()->getManager()->persist($route_list);
            }
            return $this->form_helper->finishFormHandling(
                $message,
                'manage_route_lists_year',
                ['id' => $route_list->train_table_year->id]
            );
        }

        return $this->template_helper->render('manageRouteLists/item.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer treinnummerlijst',
            'route_list' => $route_list,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    private function getOrCreateRouteList(int $year_id, int $id): RouteList
    {
        if ($id > 0) {
            /**
             * @var RouteList $route_list
             */
            $route_list = $this->form_helper->getDoctrine()->getRepository(RouteList::class)->find($id);
            if (null === $route_list) {
                throw new AccessDeniedException('This route_list does not exist');
            }

            return $route_list;
        }

        $train_table_year = $this->train_table_year_repository->find($year_id);
        if (null === $train_table_year) {
            throw new AccessDeniedException('This train_table_year does not exist');
        }

        $route_list = new RouteList();
        $route_list->train_table_year = $train_table_year;
        $route_list->transporter = $this->form_helper->getDoctrine()->getRepository(Transporter::class)->findOneBy([]);
        $route_list->characteristic = $this->form_helper
            ->getDoctrine()
            ->getRepository(Characteristic::class)
            ->findOneBy([]);

        return $route_list;
    }
}
