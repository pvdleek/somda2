<?php

namespace App\Helpers;

use App\Entity\Location;
use App\Entity\Route;
use App\Entity\RouteTrain;
use App\Entity\TrainTable;
use App\Entity\TrainTableYear;
use App\Traits\DateTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class TrainTableHelper
{
    use DateTrait;

    /**
     * @var TrainTableYear|null
     */
    private ?TrainTableYear $trainTableYear = null;

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
    private array $errorMessages = [];

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function setTrainTableYear(int $trainTableYearId)
    {
        $this->trainTableYear = $this->doctrine->getRepository(TrainTableYear::class)->find($trainTableYearId);
    }

    public function getTrainTableYear(): ?TrainTableYear
    {
        return $this->trainTableYear;
    }

    public function setRoute(string $routeNumber): void
    {
        $this->route = $this->doctrine->getRepository(Route::class)->findOneBy(['number' => $routeNumber]);
    }

    public function getRoute(): ?Route
    {
        return $this->route;
    }

    public function setLocation(string $locationName): void
    {
        $this->location = $this->doctrine->getRepository(Location::class)->findOneByName($locationName);
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    private function addErrorMessage(string $message): void
    {
        if (!\in_array($message, $this->errorMessages)) {
            $this->errorMessages[] = $message;
        }
    }

    /**
     * @return string[]
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    public function clearErrorMessages()
    {
        $this->errorMessages = [];
    }

    /**
     * @return TrainTable[]
     */
    public function getTrainTableLines(): array
    {
        $this->clearErrorMessages();
        if (\is_null($this->getTrainTableYear())) {
            $this->addErrorMessage($this->translator->trans('general.error.trainTableIndex'));
            return [];
        }
        if (\is_null($this->getRoute())) {
            $this->addErrorMessage($this->translator->trans('general.error.route'));
            return [];
        }

        return $this->doctrine->getRepository(TrainTable::class)->findBy(
            ['trainTableYear' => $this->getTrainTableYear(), 'route' => $this->getRoute()],
            ['order' => 'ASC']
        );
    }

    /**
     * @return RouteTrain[]
     */
    public function getRoutePredictions(): array
    {
        $this->clearErrorMessages();
        if (\is_null($this->getTrainTableYear())) {
            $this->addErrorMessage($this->translator->trans('general.error.trainTableIndex'));
            return [];
        }
        if (\is_null($this->getRoute())) {
            $this->addErrorMessage($this->translator->trans('general.error.route'));
            return [];
        }

        return $this->doctrine->getRepository(RouteTrain::class)->findBy(
            ['trainTableYear' => $this->getTrainTableYear(), 'route' => $this->getRoute()],
            ['dayNumber' => 'ASC']
        );
    }

    public function getPassingRoutes(int $dayNumber = null, string $startTime = null, string $endTime = null): array
    {
        $this->clearErrorMessages();
        if (\is_null($this->getTrainTableYear())) {
            $this->addErrorMessage($this->translator->trans('general.error.trainTableIndex'));
            return [];
        }
        if (\is_null($this->getLocation())) {
            $this->addErrorMessage($this->translator->trans('general.error.location'));
            return [];
        }

        if (\is_null($dayNumber)) {
            $dayNumber = date('N');
        }

        if (!\is_null($startTime)) {
            $startTimeDatabase = $this->timeDisplayToDatabase($startTime);
        } else {
            $startTimeDatabase = $this->timeDisplayToDatabase(date('H:i'));
        }
        if (!\is_null($endTime)) {
            $endTimeDatabase = $this->timeDisplayToDatabase($endTime);
            if ($startTimeDatabase > $endTimeDatabase) {
                $this->addErrorMessage($this->translator->trans('passingRoutes.error.dayBorderCrossed'));
                $endTimeDatabase = 1440;
            }
        } else {
            $endTimeDatabase = $startTimeDatabase + 120;
        }

        return $this->doctrine->getRepository(TrainTable::class)->findPassingRoutes(
            $this->getTrainTableYear(),
            $this->getLocation(),
            $dayNumber,
            $startTimeDatabase,
            $endTimeDatabase
        );
    }
}
