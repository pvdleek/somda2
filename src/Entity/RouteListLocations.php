<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_tdr_route")
 * @ORM\Entity
 */
class RouteListLocations
{
    /**
     * @var TrainTable
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="tdr_nr", referencedColumnName="tdr_nr")
     * @ORM\Id
     */
    private $trainTableYear;

    /**
     * @var RouteList
     * @ORM\ManyToOne(targetEntity="App\Entity\RouteList")
     * @ORM\JoinColumn(name="treinnummerlijst_id", referencedColumnName="id")
     * @ORM\Id
     */
    private $routeList;

    /**
     * @var int
     * @ORM\Column(name="type", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $type;

    /**
     * @var int
     * @ORM\Column(name="volgorde", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $order;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="locatieid", referencedColumnName="afkid")
     */
    private $location;

    /**
     * @return TrainTable
     */
    public function getTrainTableYear(): TrainTable
    {
        return $this->trainTableYear;
    }

    /**
     * @param TrainTable $trainTableYear
     * @return RouteListLocations
     */
    public function setTrainTableYear(TrainTable $trainTableYear): RouteListLocations
    {
        $this->trainTableYear = $trainTableYear;
        return $this;
    }

    /**
     * @return RouteList
     */
    public function getRouteList(): RouteList
    {
        return $this->routeList;
    }

    /**
     * @param RouteList $routeList
     * @return RouteListLocations
     */
    public function setRouteList(RouteList $routeList): RouteListLocations
    {
        $this->routeList = $routeList;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return RouteListLocations
     */
    public function setType(int $type): RouteListLocations
    {
        $this->type = $type;
        return $this;
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
     * @return RouteListLocations
     */
    public function setOrder(int $order): RouteListLocations
    {
        $this->order = $order;
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
     * @return RouteListLocations
     */
    public function setLocation(Location $location): RouteListLocations
    {
        $this->location = $location;
        return $this;
    }
}
