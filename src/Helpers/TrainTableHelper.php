<?php

namespace App\Helpers;

use App\Entity\Location;
use App\Entity\Route;
use App\Entity\RouteTrain;
use App\Entity\TrainTable;
use App\Entity\TrainTableYear;
use App\Repository\LocationRepository;
use App\Repository\TrainTableRepository;
use App\Traits\DateTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class TrainTableHelper
{
    use DateTrait;

    /**
     * @var TrainTableYear|null
     */
    private ?TrainTableYear $train_table_year = null;

    /**
     * @var Route|null
     */
    private ?Route $route = null;

    /**
     * @var Location|null
     */
    private ?Location $location = null;

    /**
     * @var string[]
     */
    private array $error_messages = [];

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly TranslatorInterface $translator,
        private readonly LocationRepository $location_repository,
        private readonly TrainTableRepository $train_table_repository,
    ) {
    }

    public function setTrainTableYear(int $train_table_year_id): void
    {
        $this->train_table_year = $this->doctrine->getRepository(TrainTableYear::class)->find($train_table_year_id);
    }

    public function getTrainTableYear(): ?TrainTableYear
    {
        return $this->train_table_year;
    }

    public function setRoute(string $route_number): void
    {
        $this->route = $this->doctrine->getRepository(Route::class)->findOneBy(['number' => $route_number]);
    }

    public function getRoute(): ?Route
    {
        return $this->route;
    }

    public function setLocation(string $location_name): void
    {
        $this->location = $this->location_repository->findOneByName($location_name);
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    private function addErrorMessage(string $message): void
    {
        if (!\in_array($message, $this->error_messages)) {
            $this->error_messages[] = $message;
        }
    }

    /**
     * @return string[]
     */
    public function getErrorMessages(): array
    {
        return $this->error_messages;
    }

    public function clearErrorMessages(): void
    {
        $this->error_messages = [];
    }

    /**
     * @return TrainTable[]
     */
    public function getTrainTableLines(): array
    {
        $this->clearErrorMessages();
        if (null === $this->getTrainTableYear()) {
            $this->addErrorMessage($this->translator->trans('general.error.trainTableIndex'));
            return [];
        }
        if (null === $this->getRoute()) {
            $this->addErrorMessage($this->translator->trans('general.error.route'));
            return [];
        }

        return $this->doctrine->getRepository(TrainTable::class)->findBy(
            ['train_table_year' => $this->getTrainTableYear(), 'route' => $this->getRoute()],
            ['order' => 'ASC']
        );
    }

    /**
     * @return RouteTrain[]
     */
    public function getRoutePredictions(): array
    {
        $this->clearErrorMessages();
        if (null === $this->getTrainTableYear()) {
            $this->addErrorMessage($this->translator->trans('general.error.trainTableIndex'));
            return [];
        }
        if (null === $this->getRoute()) {
            $this->addErrorMessage($this->translator->trans('general.error.route'));
            return [];
        }

        return $this->doctrine->getRepository(RouteTrain::class)->findBy(
            ['train_table_year' => $this->getTrainTableYear(), 'route' => $this->getRoute()],
            ['day_number' => 'ASC']
        );
    }

    public function getPassingRoutes(?int $day_number = null, ?string $start_time = null, ?string $end_time = null): array
    {
        $this->clearErrorMessages();
        if (null === $this->getTrainTableYear()) {
            $this->addErrorMessage($this->translator->trans('general.error.trainTableIndex'));
            return [];
        }
        if (null === $this->getLocation()) {
            $this->addErrorMessage($this->translator->trans('general.error.location'));
            return [];
        }

        if (null === $day_number) {
            $day_number = date('N');
        }

        if (null !== $start_time) {
            $start_time_database = $this->timeDisplayToDatabase($start_time);
        } else {
            $start_time_database = $this->timeDisplayToDatabase(date('H:i'));
        }
        if (null !== $end_time) {
            $end_time_database = $this->timeDisplayToDatabase($end_time);
            if ($start_time_database > $end_time_database) {
                $this->addErrorMessage($this->translator->trans('passingRoutes.error.dayBorderCrossed'));
                $end_time_database = 1440;
            }
        } else {
            $end_time_database = $start_time_database + 120;
        }

        return $this->train_table_repository->findPassingRoutes(
            $this->getTrainTableYear(),
            $this->getLocation(),
            $day_number,
            $start_time_database,
            $end_time_database
        );
    }
}
