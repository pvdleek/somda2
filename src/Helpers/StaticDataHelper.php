<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\Jargon;
use App\Entity\Location;
use App\Entity\TrainsForForum;
use App\Entity\User;
use App\Repository\TrainTableRepository;
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

        /** @var Location $location */
        foreach ($this->doctrine->getRepository(Location::class)->findAll() as $location) {
            $this->locations[$location->name] = $location->description;
        }

        /** @var Jargon $jargon */
        foreach ($this->doctrine->getRepository(Jargon::class)->findAll() as $jargon) {
            $this->locations[$jargon->term] = $jargon->description;
        }

        /** @var UserRepository $user_repository */
        $user_repository = $this->doctrine->getRepository(User::class);
        foreach ($user_repository->findActiveForStaticData() as $user) {
            $this->users['@'.$user['username']] = \strlen($user['name'] ?? '') > 0 ? $user['name'] : $user['username'];
        }

        /** @var TrainsForForum $train */
        foreach ($this->doctrine->getRepository(TrainsForForum::class)->findAll() as $train) {
            $this->routes[$train->train_number] = \sprintf(
                $this->translator->trans('trainTable.forum.route'),
                $train->train_number,
                $train->characteristic_name,
                $train->characteristic_description,
                $train->transporter_name,
                $train->first_location_name,
                $this->timeDatabaseToDisplay($train->first_location_time),
                $train->last_location_name,
                $this->timeDatabaseToDisplay($train->last_location_time),
            );
            $this->addSeriesRouteNumber([
                TrainTableRepository::FIELD_ROUTE_NUMBER => $train->train_number,
                TrainTableRepository::FIELD_CHARACTERISTIC_NAME => $train->characteristic_name,
                TrainTableRepository::FIELD_CHARACTERISTIC_DESCRIPTION => $train->characteristic_description,
                TrainTableRepository::FIELD_TRANSPORTER_NAME => $train->transporter_name,
                'first_location' => $train->first_location_name,
                'first_time' => $train->first_location_time,
                'last_location' => $train->last_location_name,
                'last_time' => $train->last_location_time,
                TrainTableRepository::FIELD_SECTION => $train->section,
            ]);
        }
    }

    private function addSeriesRouteNumber(array $route): void
    {
        $series_route_number = (int) (100 * \floor($route[TrainTableRepository::FIELD_ROUTE_NUMBER] / 100));
        if (!isset($this->routes[$series_route_number])) {
            if (null !== $route[TrainTableRepository::FIELD_SECTION]
                && \strlen($route[TrainTableRepository::FIELD_SECTION]) > 0
            ) {
                $this->routes[$series_route_number] = \sprintf(
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
