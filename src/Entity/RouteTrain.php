<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_tdr_trein_mat")
 * @ORM\Entity
 */
class RouteTrain
{
    /**
     * @var TrainTable
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="tdr_nr", referencedColumnName="tdr_nr")
     * @ORM\Id
     */
    private $trainTableYear;

    /**
     * @var Route
     * @ORM\ManyToOne(targetEntity="App\Entity\Route")
     * @ORM\JoinColumn(name="treinid", referencedColumnName="treinid")
     * @ORM\Id
     */
    private $route;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="App\Entity\Position")
     * @ORM\JoinColumn(name="posid", referencedColumnName="posid")
     * @ORM\Id
     */
    private $position;

    /**
     * @var int
     * @ORM\Column(name="dag", type="bigint", nullable=false)
     * @ORM\Id
     */
    private $dayNumber;

    /**
     * @var int
     * @ORM\Column(name="spots", type="bigint", nullable=false)
     */
    private $numberOfSpots = 0;

    /**
     * @var TrainNameRange
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainNameRange")
     * @ORM\JoinColumn(name="mat_naam_id", referencedColumnName="id")
     */
    private $trainNameRange;

    /**
     * @var TrainTypeNamePattern
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTypeNamePattern")
     * @ORM\JoinColumn(name="mat_type_id", referencedColumnName="id")
     */
    private $trainTypeNamePattern;

    /**
     * @return TrainTable
     */
    public function getTrainTableYear(): TrainTable
    {
        return $this->trainTableYear;
    }

    /**
     * @param TrainTable $trainTableYear
     * @return RouteTrain
     */
    public function setTrainTableYear(TrainTable $trainTableYear): RouteTrain
    {
        $this->trainTableYear = $trainTableYear;
        return $this;
    }

    /**
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * @param Route $route
     * @return RouteTrain
     */
    public function setRoute(Route $route): RouteTrain
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position
    {
        return $this->position;
    }

    /**
     * @param Position $position
     * @return RouteTrain
     */
    public function setPosition(Position $position): RouteTrain
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return int
     */
    public function getDayNumber(): int
    {
        return $this->dayNumber;
    }

    /**
     * @param int $dayNumber
     * @return RouteTrain
     */
    public function setDayNumber(int $dayNumber): RouteTrain
    {
        $this->dayNumber = $dayNumber;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfSpots(): int
    {
        return $this->numberOfSpots;
    }

    /**
     * @param int $numberOfSpots
     * @return RouteTrain
     */
    public function setNumberOfSpots(int $numberOfSpots): RouteTrain
    {
        $this->numberOfSpots = $numberOfSpots;
        return $this;
    }

    /**
     * @return TrainNameRange
     */
    public function getTrainNameRange(): TrainNameRange
    {
        return $this->trainNameRange;
    }

    /**
     * @param TrainNameRange $trainNameRange
     * @return RouteTrain
     */
    public function setTrainNameRange(TrainNameRange $trainNameRange): RouteTrain
    {
        $this->trainNameRange = $trainNameRange;
        return $this;
    }

    /**
     * @return TrainTypeNamePattern
     */
    public function getTrainTypeNamePattern(): TrainTypeNamePattern
    {
        return $this->trainTypeNamePattern;
    }

    /**
     * @param TrainTypeNamePattern $trainTypeNamePattern
     * @return RouteTrain
     */
    public function setTrainTypeNamePattern(TrainTypeNamePattern $trainTypeNamePattern): RouteTrain
    {
        $this->trainTypeNamePattern = $trainTypeNamePattern;
        return $this;
    }
}
