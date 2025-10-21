<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\Jargon;
use App\Entity\Location;
use App\Entity\TrainTable;
use App\Entity\TrainTableYear;
use App\Entity\User;
use App\Repository\TrainTableRepository;
use App\Repository\TrainTableYearRepository;
use App\Repository\UserRepository;
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
        if (null === $this->locations) {
            $this->loadStaticData();
        }
        return $this->locations;
    }

    /**
     * @throws \Exception
     */
    public function getUsers(): array
    {
        if (null === $this->users) {
            $this->loadStaticData();
        }
        return $this->users;
    }

    /**
     * @throws \Exception
     */
    public function getRoutes(): array
    {
        if (null === $this->routes) {
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
         * @var Location[] $location_array
         */
        $location_array = $this->doctrine->getRepository(Location::class)->findAll();
        foreach ($location_array as $location) {
            $this->locations[$location->name] = $location->description;
        }

        /**
         * @var Jargon[] $jargon_array
         */
        $jargon_array = $this->doctrine->getRepository(Jargon::class)->findAll();
        foreach ($jargon_array as $jargon) {
            $this->locations[$jargon->term] = $jargon->description;
        }

        /**
         * @var UserRepository $user_repository
         */
        $user_repository = $this->doctrine->getRepository(User::class);
        /**
         * @var User[] $user_array
         */
        $user_array = $user_repository->findActiveForStaticData();
        foreach ($user_array as $user) {
            $this->users['@'.$user['username']] =
                null !== $user['name'] && \strlen($user['name']) > 0 ? $user['name'] : $user['username'];
        }

        /**
         * @var TrainTableRepository $train_table_repository
         */
        $train_table_repository = $this->doctrine->getRepository(TrainTable::class);
        /**
         * @var TrainTableYearRepository $train_table_year_repository
         */
        $train_table_year_repository = $this->doctrine->getRepository(TrainTableYear::class);

        $route_array = $train_table_repository->findAllTrainTablesForForum($train_table_year_repository->findTrainTableYearByDate(new \DateTime()));
        $route_translation = $this->translator->trans('trainTable.forum.route');
        foreach ($route_array as $route) {
            $this->routes[$route[TrainTableRepository::FIELD_ROUTE_NUMBER]] = sprintf(
                $route_translation,
                $route[TrainTableRepository::FIELD_ROUTE_NUMBER],
                $route[TrainTableRepository::FIELD_CHARACTERISTIC_NAME],
                $route[TrainTableRepository::FIELD_CHARACTERISTIC_DESCRIPTION],
                $route[TrainTableRepository::FIELD_TRANSPORTER_NAME],
                $route['first_location'],
                $this->timeDatabaseToDisplay($route['first_time']),
                $route['last_location'],
                $this->timeDatabaseToDisplay($route['last_time']),
            );
            $this->addSeriesRouteNumber($route);
        }
    }

    private function addSeriesRouteNumber(array $route): void
    {
        $series_route_number = (int) (100 * \floor($route[TrainTableRepository::FIELD_ROUTE_NUMBER] / 100));
        if (!isset($this->routes[$series_route_number])) {
            if (null !== $route[TrainTableRepository::FIELD_SECTION]
                && \strlen($route[TrainTableRepository::FIELD_SECTION]) > 0
            ) {
                $this->routes[$series_route_number] = sprintf(
                    $this->translator->trans('trainTable.forum.seriesWithSection'),
                    $series_route_number,
                    $route[TrainTableRepository::FIELD_CHARACTERISTIC_NAME],
                    $route[TrainTableRepository::FIELD_CHARACTERISTIC_DESCRIPTION],
                    $route[TrainTableRepository::FIELD_TRANSPORTER_NAME],
                    $route[TrainTableRepository::FIELD_SECTION]
                );
                return;
            }

            $this->routes[$series_route_number] = \sprintf(
                $this->translator->trans('trainTable.forum.series'),
                $series_route_number,
                $route[TrainTableRepository::FIELD_CHARACTERISTIC_NAME],
                $route[TrainTableRepository::FIELD_CHARACTERISTIC_DESCRIPTION],
                $route[TrainTableRepository::FIELD_TRANSPORTER_NAME],
                $route['first_location'],
                $this->timeDatabaseToDisplay($route['first_time']),
                $route['last_location'],
                $this->timeDatabaseToDisplay($route['last_time'])
            );
        }
    }
}
