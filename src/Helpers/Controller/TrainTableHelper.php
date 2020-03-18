<?php

namespace App\Helpers\Controller;

use App\Entity\RouteTrain;
use App\Entity\TrainTable;
use App\Helpers\DateHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class TrainTableHelper extends BaseControllerHelper
{
    /**
     * @var DateHelper
     */
    private $dateHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param TranslatorInterface $translator
     * @param DateHelper $dateHelper
     */
    public function __construct(ManagerRegistry $doctrine, TranslatorInterface $translator, DateHelper $dateHelper)
    {
        parent::__construct($doctrine, $translator);

        $this->dateHelper = $dateHelper;
    }

    /**
     * @return TrainTable[]
     */
    public function getTrainTableLines() : array
    {
        $this->clearErrorMessages();
        if (is_null($this->getTrainTableYear())) {
            $this->addErrorMessage($this->translator->trans('general.error.trainTableIndex'));
            return [];
        }
        if (is_null($this->getRoute())) {
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
    public function getRoutePredictions() : array
    {
        $this->clearErrorMessages();
        if (is_null($this->getTrainTableYear())) {
            $this->addErrorMessage($this->translator->trans('general.error.trainTableIndex'));
            return [];
        }
        if (is_null($this->getRoute())) {
            $this->addErrorMessage($this->translator->trans('general.error.route'));
            return [];
        }

        return $this->doctrine->getRepository(RouteTrain::class)->findBy(
            ['trainTableYear' => $this->getTrainTableYear(), 'route' => $this->getRoute()],
            ['dayNumber' => 'ASC']
        );
    }

    /**
     * @param int $dayNumber
     * @param string $startTime
     * @param string $endTime
     * @return array
     */
    public function getPassingRoutes(int $dayNumber = null, string $startTime = null, string $endTime = null) : array
    {
        $this->clearErrorMessages();
        if (is_null($this->getTrainTableYear())) {
            $this->addErrorMessage($this->translator->trans('general.error.trainTableIndex'));
            return [];
        }
        if (is_null($this->getLocation())) {
            $this->addErrorMessage($this->translator->trans('general.error.location'));
            return [];
        }

        $startTimeDatabase = $this->dateHelper->timeDisplayToDatabase($startTime);
        $endTimeDatabase = $this->dateHelper->timeDisplayToDatabase($endTime);
        if ($startTimeDatabase > $endTimeDatabase) {
            $this->addErrorMessage($this->translator->trans('passingRoutes.error.dayBorderCrossed'));
            $endTimeDatabase = 1440;
        }

        return $this->doctrine->getRepository(TrainTable::class)->findPassingRoutes(
            $this->getTrainTableYear(),
            $this->getLocation(),
            $dayNumber,
            $this->dateHelper->getDayName($dayNumber),
            $startTimeDatabase,
            $endTimeDatabase
        );
    }
}
