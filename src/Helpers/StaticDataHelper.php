<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\Jargon;
use App\Entity\Location;
use App\Entity\TrainTable;
use App\Entity\TrainTableYear;
use App\Entity\User;
use App\Repository\TrainTable as TrainTableRepository;
use App\Traits\DateTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class StaticDataHelper implements RuntimeExtensionInterface
{
    use DateTrait;

    private ?array $locations = null;

    private ?array $users = null;

    private ?array $routes = null;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function getLocations(): array
    {
        if (\is_null($this->locations)) {
            $this->loadStaticData();
        }
        return $this->locations;
    }

    /**
     * @throws \Exception
     */
    public function getUsers(): array
    {
        if (\is_null($this->users)) {
            $this->loadStaticData();
        }
        return $this->users;
    }

    /**
     * @throws \Exception
     */
    public function getRoutes(): array
    {
        if (\is_null($this->routes)) {
            $this->loadStaticData();
        }
        return $this->routes;
    }

    /**
     * @throws \Exception
     */
    private function loadStaticData(): void
    {
        $this->locations = [];
        $this->users = [];
        $this->routes = [];

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

        $userArray = $this->doctrine->getRepository(User::class)->findActiveForStaticData();
        foreach ($userArray as $user) {
            $this->users['@' . $user['username']] =
                !\is_null($user['name']) && \strlen($user['name']) > 0 ? $user['name'] : $user['username'];
        }

        $routeArray = $this->doctrine->getRepository(TrainTable::class)->findAllTrainTablesForForum(
            $this->doctrine->getRepository(TrainTableYear::class)->findTrainTableYearByDate(new \DateTime())
        );
        $routeTranslation = $this->translator->trans('trainTable.forum.route');
        foreach ($routeArray as $route) {
            $this->routes[$route[TrainTableRepository::FIELD_ROUTE_NUMBER]] = sprintf(
                $routeTranslation,
                $route[TrainTableRepository::FIELD_ROUTE_NUMBER],
                $route[TrainTableRepository::FIELD_CHARACTERISTIC_NAME],
                $route[TrainTableRepository::FIELD_CHARACTERISTIC_DESCRIPTION],
                $route[TrainTableRepository::FIELD_TRANSPORTER_NAME],
                $route['firstLocation'],
                $this->timeDatabaseToDisplay($route['firstTime']),
                $route['lastLocation'],
                $this->timeDatabaseToDisplay($route['lastTime']),
            );
            $this->addSeriesRouteNumber($route);
        }
    }

    private function addSeriesRouteNumber(array $route): void
    {
        $seriesRouteNumber = 100 * \floor($route[TrainTableRepository::FIELD_ROUTE_NUMBER] / 100);
        if (!isset($this->routes[$seriesRouteNumber])) {
            if (!\is_null($route[TrainTableRepository::FIELD_SECTION])
                && \strlen($route[TrainTableRepository::FIELD_SECTION]) > 0
            ) {
                $this->routes[$seriesRouteNumber] = sprintf(
                    $this->translator->trans('trainTable.forum.seriesWithSection'),
                    $seriesRouteNumber,
                    $route[TrainTableRepository::FIELD_CHARACTERISTIC_NAME],
                    $route[TrainTableRepository::FIELD_CHARACTERISTIC_DESCRIPTION],
                    $route[TrainTableRepository::FIELD_TRANSPORTER_NAME],
                    $route[TrainTableRepository::FIELD_SECTION]
                );
                return;
            }

            $this->routes[$seriesRouteNumber] = \sprintf(
                $this->translator->trans('trainTable.forum.series'),
                $seriesRouteNumber,
                $route[TrainTableRepository::FIELD_CHARACTERISTIC_NAME],
                $route[TrainTableRepository::FIELD_CHARACTERISTIC_DESCRIPTION],
                $route[TrainTableRepository::FIELD_TRANSPORTER_NAME],
                $route['firstLocation'],
                $this->timeDatabaseToDisplay($route['firstTime']),
                $route['lastLocation'],
                $this->timeDatabaseToDisplay($route['lastTime'])
            );
        }
    }
}
