<?php

namespace App\Controller;

use App\Entity\SpecialRoute;
use App\Entity\TrainTableYear;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class TrainTableController extends BaseController
{
    /**
     * @param int|null $trainTableIndexNumber
     * @param string|null $routeNumber
     * @return Response
     * @throws Exception
     */
    public function indexAction(int $trainTableIndexNumber = null, string $routeNumber = null): Response
    {
        $this->breadcrumbHelper->addPart('general.navigation.trainTable.home', 'train_table_home');
        $this->breadcrumbHelper->addPart('general.navigation.trainTable.index', 'train_table', [], true);

        $submit = false;

        if (is_null($trainTableIndexNumber)) {
            $trainTableIndexNumber = $this->trainTableHelper->getDefaultTrainTableYear()->getId();
        } else {
            $submit = true;
            $this->trainTableHelper->setTrainTableYear($trainTableIndexNumber);
            $this->trainTableHelper->setRoute($routeNumber);
        }
        return $this->render('trainTable/index.html.twig', [
            'trainTableIndices' => $this->doctrine->getRepository(TrainTableYear::class)->findAll(),
            'trainTableIndexNumber' => $trainTableIndexNumber,
            'routeNumber' => $routeNumber,
            'trainTableLines' => $submit ? $this->trainTableHelper->getTrainTableLines() : [],
            'routePredictions' => $submit ? $this->trainTableHelper->getRoutePredictions() : [],
            'errorMessages' => $this->trainTableHelper->getErrorMessages(),
        ]);
    }

    /**
     * @param int|null $trainTableIndexNumber
     * @param string|null $locationName
     * @param int|null $dayNumber
     * @param string|null $startTime
     * @param string|null $endTime
     * @return Response
     * @throws Exception
     */
    public function passingRoutesAction(
        int $trainTableIndexNumber = null,
        string $locationName = null,
        int $dayNumber = null,
        string $startTime = null,
        string $endTime = null
    ): Response {
        $this->breadcrumbHelper->addPart('general.navigation.trainTable.home', 'train_table_home');
        $this->breadcrumbHelper->addPart('general.navigation.trainTable.passingRoutes', 'passing_routes', [], true);

        $submit = false;
        if (is_null($trainTableIndexNumber)) {
            $trainTableIndexNumber = $this->trainTableHelper->getDefaultTrainTableYear()->getId();
        } else {
            $submit = true;
            $this->trainTableHelper->setTrainTableYear($trainTableIndexNumber);
            $this->trainTableHelper->setLocation($locationName);
        }
        return $this->render('trainTable/passingRoutes.html.twig', [
            'trainTableIndices' => $this->doctrine->getRepository(TrainTableYear::class)->findAll(),
            'trainTableIndexNumber' => $trainTableIndexNumber,
            'trainTableIndex' => $this->trainTableHelper->getTrainTableYear(),
            'locationName' => $locationName,
            'dayNumber' => $dayNumber,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'passingRoutes' => $submit ?
                $this->trainTableHelper->getPassingRoutes($dayNumber, $startTime, $endTime) : [],
            'errorMessages' => $this->trainTableHelper->getErrorMessages(),
        ]);
    }

    /**
     * @param int|null $id
     * @return Response
     */
    public function specialRoutesAction(int $id = null): Response
    {
        $specialRoute = null;
        if (!is_null($id)) {
            $specialRoute = $this->doctrine->getRepository(SpecialRoute::class)->find($id);
        }

        $this->breadcrumbHelper->addPart('general.navigation.trainTable.home', 'dienstregeling_home');
        $this->breadcrumbHelper->addPart(
            'general.navigation.trainTable.specialRoutes',
            'special_routes',
            [],
            is_null($specialRoute)
        );

        if (is_null($specialRoute)) {
            $specialRoutes = $this->doctrine->getRepository(SpecialRoute::class)->findBy(
                [], ['startDate' => 'DESC']
            );
            return $this->render('trainTable/specialRoutes.html.twig', [
                'specialRoutes' => $specialRoutes
            ]);
        } else {
            $this->breadcrumbHelper->addPart(
                'general.navigation.trainTable.specialRoute',
                'special_route',
                ['id' => $id],
                true
            );
            return $this->render('trainTable/specialRoute.html.twig', ['specialRoute' => $specialRoute]);
        }
    }
}
