<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_spots_extra")
 * @ORM\Entity
 */
class SpotExtra
{
    /**
     * @var Spot
     * @ORM\OneToOne(targetEntity="App\Entity\Spot", inversedBy="extra")
     * @ORM\JoinColumn(name="spotid", referencedColumnName="spotid")
     * @ORM\Id
     */
    public $spot;

    /**
     * @var string
     * @ORM\Column(name="extra", type="string", length=255, nullable=false)
     */
    public $extra = '';

    /**
     * @var string
     * @ORM\Column(name="user_extra", type="string", length=255, nullable=false)
     */
    public $userExtra = '';
}
