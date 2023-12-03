<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_tdr_route")
 * @ORM\Entity
 */
class RouteListLocations
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="tdr_nr", referencedColumnName="tdr_nr")
     * @ORM\Id
     */
    public ?TrainTable $trainTableYear = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RouteList")
     * @ORM\JoinColumn(name="treinnummerlijst_id", referencedColumnName="id")
     * @ORM\Id
     */
    public ?RouteList $routeList = null;

    /**
     * @ORM\Column(name="type", type="smallint", nullable=false, options={"default"="1", "unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    public int $type = 1;

    /**
     * @ORM\Column(name="volgorde", type="smallint", nullable=false, options={"default"="1", "unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    public int $order = 1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="locatieid", referencedColumnName="afkid")
     */
    public ?Location $location = null;
}
