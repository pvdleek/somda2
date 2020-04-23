<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_spot_punt_text")
 * @ORM\Entity
 */
class PoiText
{
    /**
     * @var Poi
     * @ORM\OneToOne(targetEntity="App\Entity\Poi", inversedBy="text")
     * @ORM\JoinColumn(name="puntid", referencedColumnName="puntid")
     * @ORM\Id
     */
    public Poi $poi;

    /**
     * @var string
     * @ORM\Column(name="route_auto", type="text", length=0, nullable=false)
     */
    public string $routeCar;

    /**
     * @var string
     * @ORM\Column(name="route_ov", type="text", length=0, nullable=false)
     */
    public string $routePublicTransport;

    /**
     * @var string
     * @ORM\Column(name="bijzonderheden", type="text", length=0, nullable=false)
     */
    public string $particularities;
}
