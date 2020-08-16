<?php

namespace App\Controller;

use App\Entity\Characteristic;
use App\Entity\RouteList;
use App\Entity\TrainTableYear;
use App\Entity\Transporter;
use App\Form\RouteList as RouteListForm;
use App\Generics\RoleGenerics;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ManageRouteListsController
{
    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @param UserHelper $userHelper
     * @param FormHelper $formHelper
     * @param TemplateHelper $templateHelper
     */
    public function __construct(UserHelper $userHelper, FormHelper $formHelper, TemplateHelper $templateHelper)
    {
        $this->userHelper = $userHelper;
        $this->formHelper = $formHelper;
        $this->templateHelper = $templateHelper;
    }

    /**
     * @param int|null $id
     * @return Response
     * @throws Exception
     */
    public function routeListsAction(int $id = null): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_ROUTE_NUMBER_LIST);

        if (is_null($id)) {
            $trainTableYear = $this->formHelper
                ->getDoctrine()
                ->getRepository(TrainTableYear::class)
                ->findTrainTableYearByDate(new DateTime());
            $routeLists = [];
        } else {
            $trainTableYear = $this->formHelper->getDoctrine()->getRepository(TrainTableYear::class)->find($id);
            if (is_null($trainTableYear)) {
                throw new AccessDeniedException('This trainTableYear does not exist');
            }

            $routeLists = $this->formHelper->getDoctrine()->getRepository(RouteList::class)->findBy(
                ['trainTableYear' => $trainTableYear],
                ['firstNumber' => 'ASC']
            );
        }

        return $this->templateHelper->render('manageRouteLists/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer treinnummerlijst',
            'trainTableYears' => $this->formHelper
                ->getDoctrine()
                ->getRepository(TrainTableYear::class)
                ->findBy([], ['startDate' => 'DESC']),
            'trainTableYear' => $trainTableYear,
            'routeLists' => $routeLists,
        ]);
    }

    /**
     * @param Request $request
     * @param int $yearId
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function routeListAction(Request $request, int $yearId, int $id)
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_ROUTE_NUMBER_LIST);

        $routeList = $this->getOrCreateRouteList($yearId, $id);
        $form = $this->formHelper->getFactory()->create(RouteListForm::class, $routeList);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($routeList->getId() > 0) {
                $message = 'Treinnummer bijgewerkt';
            } else {
                // New routeList
                $message = 'Treinnummer toegevoegd';
                $this->formHelper->getDoctrine()->getManager()->persist($routeList);
            }
            return $this->formHelper->finishFormHandling(
                $message,
                'manage_route_lists_year',
                ['id' => $routeList->trainTableYear->getId()]
            );
        }

        return $this->templateHelper->render('manageRouteLists/item.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer treinnummerlijst',
            'routeList' => $routeList,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    /**
     * @param int $yearId
     * @param int $id
     * @return RouteList
     */
    private function getOrCreateRouteList(int $yearId, int $id): RouteList
    {
        if ($id > 0) {
            /**
             * @var RouteList $routeList
             */
            $routeList = $this->formHelper->getDoctrine()->getRepository(RouteList::class)->find($id);
            if (is_null($routeList)) {
                throw new AccessDeniedException('This routeList does not exist');
            }

            return $routeList;
        }

        $trainTableYear = $this->formHelper->getDoctrine()->getRepository(TrainTableYear::class)->find($yearId);
        if (is_null($trainTableYear)) {
            throw new AccessDeniedException('This trainTableYear does not exist');
        }

        $routeList = new RouteList();
        $routeList->trainTableYear = $trainTableYear;
        $routeList->transporter = $this->formHelper->getDoctrine()->getRepository(Transporter::class)->findOneBy([]);
        $routeList->characteristic = $this->formHelper
            ->getDoctrine()
            ->getRepository(Characteristic::class)
            ->findOneBy([]);

        return $routeList;
    }
}
