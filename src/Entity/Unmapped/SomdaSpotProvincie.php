<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaSpotProvincie
 *
 * @ORM\Table(name="somda_spot_provincie")
 * @ORM\Entity
 */
class SomdaSpotProvincie
{
    /**
     * @var int
     *
     * @ORM\Column(name="provincieid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $provincieid;

    /**
     * @var string
     *
     * @ORM\Column(name="naam", type="string", length=15, nullable=false)
     */
    private $naam = '';


}
