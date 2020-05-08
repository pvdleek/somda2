<?php

namespace App\Helpers;

use App\Entity\Jargon;
use App\Entity\Location;
use App\Entity\TrainTable;
use App\Entity\TrainTableYear;
use App\Entity\User;
use App\Traits\DateTrait;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class StaticDataHelper implements RuntimeExtensionInterface
{
    use DateTrait;

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

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
     * @param TranslatorInterface $translator
     */
    public function __construct(ManagerRegistry $doctrine, TranslatorInterface $translator)
    {
        $this->doctrine = $doctrine;
        $this->translator = $translator;
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
            $this->doctrine->getRepository(TrainTableYear::class)->findCurrentTrainTableYear()
        );
        foreach ($routeArray as $route) {
            $this->routes[$route['routeNumber']] = sprintf(
                $this->translator->trans('trainTable.forum.route'),
                $route['routeNumber'],
                $route['characteristicName'],
                $route['characteristicDescription'],
                $route['transporter'],
                $route['firstLocation'],
                $this->timeDatabaseToDisplay($route['firstTime']),
                $route['lastLocation'],
                $this->timeDatabaseToDisplay($route['lastTime']),
            );
            $this->addSeriesRouteNumber($route);
        }
    }

    /**
     * @param array $route
     */
    private function addSeriesRouteNumber(array $route): void
    {
        $seriesRouteNumber = 100 * floor($route['routeNumber'] / 100);
        if (!isset($this->routes[$seriesRouteNumber])) {
            if (strlen($route['section']) > 0) {
                $this->routes[$seriesRouteNumber] = sprintf(
                    $this->translator->trans('trainTable.forum.seriesWithSection'),
                    $seriesRouteNumber,
                    $route['characteristicName'],
                    $route['characteristicDescription'],
                    $route['transporter'],
                    $route['section']
                );
                return;
            }

            $this->routes[$seriesRouteNumber] = sprintf(
                $this->translator->trans('trainTable.forum.series'),
                $seriesRouteNumber,
                $route['characteristicName'],
                $route['characteristicDescription'],
                $route['transporter'],
                $route['firstLocation'],
                $this->timeDatabaseToDisplay($route['firstTime']),
                $route['lastLocation'],
                $this->timeDatabaseToDisplay($route['lastTime'])
            );
        }
    }
}
