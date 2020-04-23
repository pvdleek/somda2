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
    public TrainTable $trainTableYear;

    /**
     * @var RouteList
     * @ORM\ManyToOne(targetEntity="App\Entity\RouteList")
     * @ORM\JoinColumn(name="treinnummerlijst_id", referencedColumnName="id")
     * @ORM\Id
     */
    public RouteList $routeList;

    /**
     * @var int
     * @ORM\Column(name="type", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    public int $type;

    /**
     * @var int
     * @ORM\Column(name="volgorde", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    public int $order;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="locatieid", referencedColumnName="afkid")
     */
    public Location $location;
}
