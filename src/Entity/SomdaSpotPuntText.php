<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaSpotPuntText
 *
 * @ORM\Table(name="somda_spot_punt_text")
 * @ORM\Entity
 */
class SomdaSpotPuntText
{
    /**
     * @var int
     *
     * @ORM\Column(name="puntid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $puntid;

    /**
     * @var string
     *
     * @ORM\Column(name="route_auto", type="text", length=0, nullable=false)
     */
    private $routeAuto;

    /**
     * @var string
     *
     * @ORM\Column(name="route_ov", type="text", length=0, nullable=false)
     */
    private $routeOv;

    /**
     * @var string
     *
     * @ORM\Column(name="bijzonderheden", type="text", length=0, nullable=false)
     */
    private $bijzonderheden;


}
