<?php

namespace App\Helpers;

use App\Entity\Jargon;
use App\Entity\Location;
use App\Entity\TrainTable;
use App\Entity\TrainTableYear;
use App\Entity\User;
use App\Traits\DateTrait;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Twig\Extension\RuntimeExtensionInterface;

class StaticDataHelper implements RuntimeExtensionInterface
{
    use DateTrait;

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var array
     */
    private ?array $locations = null;

    /**
     * @var array
     */
    private ?array $users = null;

    /**
     * @var array
     */
    private ?array $routes = null;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getLocations(): array
    {
        if (is_null($this->locations)) {
            $this->loadStaticData();
        }
        return $this->locations;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getUsers(): array
    {
        if (is_null($this->users)) {
            $this->loadStaticData();
        }
        return $this->users;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getRoutes(): array
    {
        if (is_null($this->routes)) {
            $this->loadStaticData();
        }
        return $this->routes;
    }

    /**
     * @throws Exception
     */
    private function loadStaticData(): void
    {
        /**
         * @var Location[] $locationArray
         * @var Jargon[] $jargonArray
         * @var User[] $userArray
         */
        $locationArray = $this->doctrine->getRepository(Location::class)->findAll();
        foreach ($locationArray as $location) {
            $this->locations[$location->name] = $location->description;
        }

        $jargonArray = $this->doctrine->getRepository(Jargon::class)->findAll();
        foreach ($jargonArray as $jargon) {
            $this->locations[$jargon->term] = $jargon->description;
        }

        $userArray = $this->doctrine->getRepository(User::class)->findBy(['active' => true]);
        foreach ($userArray as $user) {
            $this->users['@' . $user->username] = strlen($user->name) > 0 ? $user->name : $user->username;
        }

        $routeArray = $this->doctrine->getRepository(TrainTable::class)->findAllTrainTablesForForum(
            $this->getDefaultTrainTableYear()
        );
        foreach ($routeArray as $route) {
            $this->routes[$route['routeNumber']] = 'Trein ' . $route['routeNumber'] . ' rijdt als ' .
                $route['characteristicName'] . ' (' . $route['characteristicDescription'] . ') voor ' .
                $route['transporter'] . ' van ' . $route['firstLocation'] . ' (' .
                $this->timeDatabaseToDisplay($route['firstTime']) . ') tot ' . $route['lastLocation'] . ' (' .
                $this->timeDatabaseToDisplay($route['lastTime']) . ')';

            $this->addSeriesRouteNumber($route);
        }
    }

    /**
     * @return TrainTableYear
     * @throws Exception
     */
    private function getDefaultTrainTableYear(): TrainTableYear
    {
        /**
         * @var TrainTableYear[] $trainTableYears
         */
        $trainTableYears = $this->doctrine->getRepository(TrainTableYear::class)->findAll();
        foreach ($trainTableYears as $trainTableYear) {
            if ($trainTableYear->startDate <= new DateTime() && $trainTableYear->endDate >= new DateTime()) {
                return $trainTableYear;
            }
        }
        return $trainTableYears[0];
    }

    /**
     * @param array $route
     */
    private function addSeriesRouteNumber(array $route): void
    {
        $seriesRouteNumber = 100 * floor($route['routeNumber'] / 100);
        if (!isset($this->routes[$seriesRouteNumber])) {
            if (strlen($route['section']) > 0) {
                $this->routes[$seriesRouteNumber] = 'Treinserie ' . $seriesRouteNumber . ' rijdt als ' .
                    $route['characteristicName'] . ' (' . $route['characteristicDescription'] . ') voor ' .
                    $route['transporter'] . ' over traject ' . $route['section'];
            } else {
                $this->routes[$seriesRouteNumber] = 'Treinserie ' . $seriesRouteNumber . ' rijdt als ' .
                    $route['characteristicName'] . ' (' . $route['characteristicDescription'] . ') voor ' .
                    $route['transporter'] . ' van ' . $route['firstLocation'] . ' (' .
                    $this->timeDatabaseToDisplay($route['firstTime']) . ') tot ' . $route['lastLocation'] . ' (' .
                    $this->timeDatabaseToDisplay($route['lastTime']) . ')';
            }
        }
    }
}
