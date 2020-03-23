<?php

namespace App\Helpers\Controller;

use App\Entity\Location;
use App\Entity\Route;
use App\Entity\TrainTableYear;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class BaseControllerHelper
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var TrainTableYear
     */
    private $trainTableYear = null;

    /**
     * @var Route
     */
    private $route = null;

    /**
     * @var Location
     */
    private $location = null;

    /**
     * @var string[]
     */
    private $errorMessages = [];

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
     * @param int $trainTableYearId
     */
    public function setTrainTableYear(int $trainTableYearId)
    {
        $this->trainTableYear = $this->doctrine->getRepository(TrainTableYear::class)->find($trainTableYearId);
    }

    /**
     * @return TrainTableYear|null
     */
    public function getTrainTableYear(): ?TrainTableYear
    {
        return $this->trainTableYear;
    }

    /**
     * @return TrainTableYear
     * @throws Exception
     */
    public function getDefaultTrainTableYear(): TrainTableYear
    {
        $trainTableYears = $this->doctrine->getRepository(TrainTableYear::class)->findAll();
        foreach ($trainTableYears as $trainTableYear) {
            if ($trainTableYear->getStartDate() <= new DateTime() && $trainTableYear->getEndDate() >= new DateTime()) {
                return $trainTableYear;
            }
        }
        return $trainTableYears[0];
    }

    /**
     * @param string $routeNumber
     */
    public function setRoute(string $routeNumber): void
    {
        $this->route = $this->doctrine->getRepository(Route::class)->findOneBy(['number' => $routeNumber]);
    }

    /**
     * @return Route|null
     */
    public function getRoute(): ?Route
    {
        return $this->route;
    }

    /**
     * @param string $locationName
     */
    public function setLocation(string $locationName): void
    {
        $this->location = $this->doctrine->getRepository(Location::class)->findOneByName($locationName);
    }

    /**
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param string $message
     */
    protected function addErrorMessage(string $message): void
    {
        if (!in_array($message, $this->errorMessages)) {
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

    /**
     *
     */
    public function clearErrorMessages()
    {
        $this->errorMessages = [];
    }
}
