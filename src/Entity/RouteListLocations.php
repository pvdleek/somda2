<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="rll_route_list_location", indexes={
 *     @ORM\Index(name="IDX_rll_tty_id", columns={"rll_tty_id"}),
 *     @ORM\Index(name="IDX_rll_rol_id", columns={"rll_rol_id"}),
 *     @ORM\Index(name="IDX_rll_loc_id", columns={"rll_loc_id"}),
 * })
 * @ORM\Entity
 */
class RouteListLocations
{
    /**
     * @var TrainTable
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="rll_tty_id", referencedColumnName="tty_id")
     * @ORM\Id
     */
    public TrainTable $trainTableYear;

    /**
     * @var RouteList
     * @ORM\ManyToOne(targetEntity="App\Entity\RouteList")
     * @ORM\JoinColumn(name="rll_rol_id", referencedColumnName="rol_id")
     * @ORM\Id
     */
    public RouteList $routeList;

    /**
     * @var int
     * @ORM\Column(name="rll_type", type="integer", nullable=false, options={"default"="1"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    public int $type = 1;

    /**
     * @var int
     * @ORM\Column(name="rll_order", type="integer", nullable=false, options={"default"="1"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    public int $order = 1;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="rll_loc_id", referencedColumnName="loc_id")
     */
    public Location $location;
}
