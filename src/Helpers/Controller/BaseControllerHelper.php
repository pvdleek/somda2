<?php

namespace App\Helpers\Controller;

use App\Entity\Location;
use App\Entity\Route;
use App\Entity\TrainTableYear;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class BaseControllerHelper
{
    /**
     * @var ManagerRegistry
     */
    protected ManagerRegistry $doctrine;

    /**
     * @var TranslatorInterface
     */
    protected TranslatorInterface $translator;

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
