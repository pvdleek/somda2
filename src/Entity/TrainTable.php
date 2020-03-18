<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_tdr", indexes={@ORM\Index(name="idx_48320_tijd", columns={"tijd"}), @ORM\Index(name="idx_48320_locatieid", columns={"locatieid"}), @ORM\Index(name="idx_48320_treinid", columns={"treinid"})})
 * @ORM\Entity(repositoryClass="App\Repository\TrainTable")
 */
class TrainTable
{
    /**
     * @var int
     * @ORM\Column(name="tdrid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="orderid", type="bigint", nullable=false)
     */
    private $order;

    /**
     * @var string
     * @ORM\Column(name="actie", type="string", length=1, nullable=false, options={"default"="-"})
     */
    private $action = '-';

    /**
     * @var int
     * @ORM\Column(name="tijd", type="bigint", nullable=false)
     */
    private $time;

    /**
     * @var string|null
     * @ORM\Column(name="spoor", type="string", length=3, nullable=true)
     */
    private $track;

    /**
     * @var TrainTableYear
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="tdr_nr", referencedColumnName="tdr_nr")
     */
    private $trainTableYear;

    /**
     * @var Route
     * @ORM\ManyToOne(targetEntity="App\Entity\Route", inversedBy="trainTables")
     * @ORM\JoinColumn(name="treinid", referencedColumnName="treinid")
     */
    private $route;

    /**
     * @var RouteOperationDays
     * @ORM\ManyToOne(targetEntity="App\Entity\RouteOperationDays")
     * @ORM\JoinColumn(name="rijdagenid", referencedColumnName="rijdagenid")
     */
    private $routeOperationDays;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="trainTables")
     * @ORM\JoinColumn(name="locatieid", referencedColumnName="afkid")
     */
    private $location;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return TrainTable
     */
    public function setOrder(int $order): TrainTable
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return TrainTable
     */
    public function setAction(string $action): TrainTable
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @param int $time
     * @return TrainTable
     */
    public function setTime(int $time): TrainTable
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrack(): ?string
    {
        return $this->track;
    }

    /**
     * @param string|null $track
     * @return TrainTable
     */
    public function setTrack(?string $track): TrainTable
    {
        $this->track = $track;
        return $this;
    }

    /**
     * @return TrainTableYear
     */
    public function getTrainTableYear(): TrainTableYear
    {
        return $this->trainTableYear;
    }

    /**
     * @param TrainTableYear $trainTableYear
     * @return TrainTable
     */
    public function setTrainTableYear(TrainTableYear $trainTableYear): TrainTable
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
     * @return TrainTable
     */
    public function setRoute(Route $route): TrainTable
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return RouteOperationDays
     */
    public function getRouteOperationDays(): RouteOperationDays
    {
        return $this->routeOperationDays;
    }

    /**
     * @param RouteOperationDays $routeOperationDays
     * @return TrainTable
     */
    public function setRouteOperationDays(RouteOperationDays $routeOperationDays): TrainTable
    {
        $this->routeOperationDays = $routeOperationDays;
        return $this;
    }

    /**
     * @return Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * @param Location $location
     * @return TrainTable
     */
    public function setLocation(Location $location): TrainTable
    {
        $this->location = $location;
        return $this;
    }
}
