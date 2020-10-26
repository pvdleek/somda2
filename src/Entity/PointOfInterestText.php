<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="pot_point_of_interest_text")
 * @ORM\Entity
 */
class PointOfInterestText
{
    /**
     * @var PointOfInterest
     * @ORM\OneToOne(targetEntity="PointOfInterest", inversedBy="text")
     * @ORM\JoinColumn(name="pot_poi_id", referencedColumnName="poi_id")
     * @ORM\Id
     */
    public PointOfInterest $poi;

    /**
     * @var string
     * @ORM\Column(name="pot_route_car", type="text", length=0, nullable=false)
     */
    public string $routeCar;

    /**
     * @var string
     * @ORM\Column(name="pot_route_public_transport", type="text", length=0, nullable=false)
     */
    public string $routePublicTransport;

    /**
     * @var string
     * @ORM\Column(name="pot_particularities", type="text", length=0, nullable=false)
     */
    public string $particularities;
}
