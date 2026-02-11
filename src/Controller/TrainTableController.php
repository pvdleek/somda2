<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\SpecialRoute;
use App\Entity\TrainTableYear;
use App\Generics\RoleGenerics;
use App\Helpers\TrainTableHelper;
use App\Helpers\FlashHelper;
use App\Helpers\RoutesDisplayHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Repository\TrainTableYearRepository;
use Doctrine\Persistence\ManagerRegistry;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class TrainTableController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly FlashHelper $flash_helper,
        private readonly RoutesDisplayHelper $routes_display_helper,
        private readonly TemplateHelper $template_helper,
        private readonly TrainTableHelper $train_table_helper,
        private readonly UserHelper $user_helper,
        private readonly TrainTableYearRepository $train_table_year_repository,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function indexAction(?int $train_table_year_id = null, ?string $route_number = null): Response
    {
        $submit = false;

        if (null === $train_table_year_id) {
            $train_table_year_id = $this->train_table_year_repository->findTrainTableYearByDate(new \DateTime())->id;
        } else {
            $submit = true;
            $this->train_table_helper->setTrainTableYear($train_table_year_id);
            $this->train_table_helper->setRoute($route_number);
        }

        $train_table_lines = $submit ? $this->train_table_helper->getTrainTableLines() : [];
        $route_predictions = $submit ? $this->train_table_helper->getRoutePredictions() : [];
        foreach ($this->train_table_helper->getErrorMessages() as $message) {
            $this->flash_helper->add(FlashHelper::FLASH_TYPE_ERROR, $message);
        }

        return $this->template_helper->render('trainTable/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Dienstregeling van een trein',
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDICES =>
                $this->doctrine->getRepository(TrainTableYear::class)->findAll(),
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDEX_NUMBER => $train_table_year_id,
            'route_number' => $route_number,
            'trainTableLines' => $train_table_lines,
            'routePredictions' => $route_predictions,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function passingRoutesAction(
        ?int $train_table_year_id = null,
        ?string $location_name = null,
        ?int $day_number = null,
        ?string $start_time = null,
        ?string $end_time = null
    ): Response {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_PASSING_ROUTES);

        if (null === $day_number) {
            $train_table_year_id = $this->train_table_year_repository->findTrainTableYearByDate(new \DateTime())->id;

            $day_number = \date('N');
            $start_time = \date('H:i', \time() - (60 * 15));
            $end_time = \date('H:i', \time() + (60 * 45));

            $passing_routes = [];
        } else {
            if ($train_table_year_id === 0) {
                $train_table_year_id = $this->train_table_year_repository->findTrainTableYearByDate(new \DateTime())->id;
            }

            $this->train_table_helper->setTrainTableYear($train_table_year_id);
            $this->train_table_helper->setLocation($location_name);

            $passing_routes = $this->train_table_helper->getPassingRoutes($day_number, $start_time, $end_time);
        }

        foreach ($this->train_table_helper->getErrorMessages() as $message) {
            $this->flash_helper->add(FlashHelper::FLASH_TYPE_ERROR, $message);
        }

        return $this->template_helper->render('trainTable/passingRoutes.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Doorkomststaat',
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDICES => $this->doctrine->getRepository(TrainTableYear::class)->findAll(),
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDEX_NUMBER => $train_table_year_id,
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDEX => $this->train_table_helper->getTrainTableYear(),
            'location_name' => $location_name,
            TemplateHelper::PARAMETER_DAY_NUMBER => $day_number,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'passingRoutes' => $passing_routes,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function passingRoutesExportAction(
        int $train_table_year_id,
        string $location_name,
        int $day_number,
        string $start_time,
        string $end_time,
        int $spotter_version,
    ): BinaryFileResponse {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_PASSING_ROUTES);

        $this->train_table_helper->setTrainTableYear($train_table_year_id);
        $this->train_table_helper->setLocation($location_name);

        $passing_routes = $this->train_table_helper->getPassingRoutes($day_number, $start_time, $end_time);

        $html = $this->template_helper->render('trainTable/passingRoutesExport.html.twig', [
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDEX_NUMBER => $train_table_year_id,
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDEX => $this->train_table_helper->getTrainTableYear(),
            'location_name' => $location_name,
            TemplateHelper::PARAMETER_DAY_NUMBER => $day_number,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'passingRoutes' => $passing_routes,
            'spotter_version' => 1 === $spotter_version,
        ])->getContent();

        $pdf_options = new Options();
        $pdf_options->set('defaultFont', 'Arial');
        $pdf_options->set('isHtml5ParserEnabled', true);

        $dom_pdf = new Dompdf($pdf_options);
        $dom_pdf->loadHtml($html);
        $dom_pdf->setPaper('A4', 'landscape');
        $dom_pdf->render();

        $temp_filename = (new Filesystem())->tempnam(\sys_get_temp_dir(), 'doorkomststaat_', '.pdf');
        \file_put_contents($temp_filename, $dom_pdf->output());

        return (new BinaryFileResponse($temp_filename, Response::HTTP_OK))->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'doorkomststaat.pdf');
    }

    public function routeOverviewAction(?int $train_table_year_id = null, ?int $route_list_id = null): Response
    {
        $routes_display = $this->routes_display_helper->getRoutesDisplay($train_table_year_id, $route_list_id);

        return $this->template_helper->render('trainTable/routeOverview.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Overzicht treinnummers',
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDICES =>
                $this->doctrine->getRepository(TrainTableYear::class)->findAll(),
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDEX_NUMBER => $routes_display->train_table_year->id,
            'route_lists' => $routes_display->route_lists,
            'selected_route_list' => $routes_display->selected_route_list,
            'routes' => $routes_display->routes,
        ]);
    }

    public function specialRoutesAction(?int $id = null): Response
    {
        $special_route = null;
        if (null !== $id) {
            $special_route = $this->doctrine->getRepository(SpecialRoute::class)->find($id);
        }

        if (null === $special_route) {
            $special_routes = $this->doctrine
                ->getRepository(SpecialRoute::class)
                ->findBy([], ['start_date' => 'DESC']);
            return $this->template_helper->render('trainTable/specialRoutes.html.twig', [
                TemplateHelper::PARAMETER_PAGE_TITLE => 'Bijzondere ritten',
                'specialRoutes' => $special_routes,
            ]);
        }

        return $this->template_helper->render('trainTable/specialRoute.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Bijzondere rit '.$special_route->title,
            'specialRoute' => $special_route,
        ]);
    }
}
