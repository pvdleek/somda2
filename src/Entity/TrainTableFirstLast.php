<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_tdr_s_e")
 * @ORM\Entity
 */
class TrainTableFirstLast
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Route", inversedBy="trainTableFirstLasts")
     * @ORM\JoinColumn(name="treinid", referencedColumnName="treinid")
     * @ORM\Id
     */
    private $route;

    /**
     * @var int
     * @ORM\Column(name="dag", type="bigint", nullable=false)
     * @ORM\Id
     */
    private $dayNumber;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="v_locatieid", referencedColumnName="afkid")
     */
    private $firstLocation;

    /**
     * @var string
     * @ORM\Column(name="v_actie", type="string", length=1, nullable=false, options={"default"="-"})
     */
    private $firstAction = '-';

    /**
     * @var int
     * @ORM\Column(name="v_tijd", type="bigint", nullable=false)
     */
    private $firstTime;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="a_locatieid", referencedColumnName="afkid")
     */
    private $lastLocation;

    /**
     * @var string
     * @ORM\Column(name="a_actie", type="string", length=1, nullable=false, options={"default"="-"})
     */
    private $lastAction = '-';

    /**
     * @var int
     * @ORM\Column(name="a_tijd", type="bigint", nullable=false)
     */
    private $lastTime;

    /**
     * @return TrainTable
     */
    public function getTrainTableYear(): TrainTable
    {
        return $this->trainTableYear;
    }

    /**
     * @param TrainTable $trainTableYear
     * @return TrainTableFirstLast
     */
    public function setTrainTableYear(TrainTable $trainTableYear): TrainTableFirstLast
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
     * @return TrainTableFirstLast
     */
    public function setRoute(Route $route): TrainTableFirstLast
    {
        $this->route = $route;
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
     * @return TrainTableFirstLast
     */
    public function setDayNumber(int $dayNumber): TrainTableFirstLast
    {
        $this->dayNumber = $dayNumber;
        return $this;
    }

    /**
     * @return Location
     */
    public function getFirstLocation(): Location
    {
        return $this->firstLocation;
    }

    /**
     * @param Location $firstLocation
     * @return TrainTableFirstLast
     */
    public function setFirstLocation(Location $firstLocation): TrainTableFirstLast
    {
        $this->firstLocation = $firstLocation;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstAction(): string
    {
        return $this->firstAction;
    }

    /**
     * @param string $firstAction
     * @return TrainTableFirstLast
     */
    public function setFirstAction(string $firstAction): TrainTableFirstLast
    {
        $this->firstAction = $firstAction;
        return $this;
    }

    /**
     * @return int
     */
    public function getFirstTime(): int
    {
        return $this->firstTime;
    }

    /**
     * @param int $firstTime
     * @return TrainTableFirstLast
     */
    public function setFirstTime(int $firstTime): TrainTableFirstLast
    {
        $this->firstTime = $firstTime;
        return $this;
    }

    /**
     * @return Location
     */
    public function getLastLocation(): Location
    {
        return $this->lastLocation;
    }

    /**
     * @param Location $lastLocation
     * @return TrainTableFirstLast
     */
    public function setLastLocation(Location $lastLocation): TrainTableFirstLast
    {
        $this->lastLocation = $lastLocation;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastAction(): string
    {
        return $this->lastAction;
    }

    /**
     * @param string $lastAction
     * @return TrainTableFirstLast
     */
    public function setLastAction(string $lastAction): TrainTableFirstLast
    {
        $this->lastAction = $lastAction;
        return $this;
    }

    /**
     * @return int
     */
    public function getLastTime(): int
    {
        return $this->lastTime;
    }

    /**
     * @param int $lastTime
     * @return TrainTableFirstLast
     */
    public function setLastTime(int $lastTime): TrainTableFirstLast
    {
        $this->lastTime = $lastTime;
        return $this;
    }
}
