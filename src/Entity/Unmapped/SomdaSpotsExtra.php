<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaSpotsExtra
 *
 * @ORM\Table(name="somda_spots_extra")
 * @ORM\Entity
 */
class SomdaSpotsExtra
{
    /**
     * @var int
     *
     * @ORM\Column(name="spotid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $spotid;

    /**
     * @var string
     *
     * @ORM\Column(name="extra", type="string", length=255, nullable=false)
     */
    private $extra = '';

    /**
     * @var string
     *
     * @ORM\Column(name="user_extra", type="string", length=255, nullable=false)
     */
    private $userExtra = '';


}
