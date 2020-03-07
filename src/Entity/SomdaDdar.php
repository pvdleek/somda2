<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaDdar
 *
 * @ORM\Table(name="somda_ddar", indexes={@ORM\Index(name="idx_47846_matid", columns={"matid"})})
 * @ORM\Entity
 */
class SomdaDdar
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="matid", type="bigint", nullable=false)
     */
    private $matid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="stam", type="bigint", nullable=true)
     */
    private $stam;

    /**
     * @var int|null
     *
     * @ORM\Column(name="afkid", type="bigint", nullable=true)
     */
    private $afkid;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="spot_ander_laatste", type="date", nullable=true)
     */
    private $spotAnderLaatste;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="spot_eerste", type="date", nullable=false)
     */
    private $spotEerste;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="spot_laatste", type="date", nullable=true)
     */
    private $spotLaatste;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="spot_ander_eerste", type="date", nullable=true)
     */
    private $spotAnderEerste;

    /**
     * @var string
     *
     * @ORM\Column(name="extra", type="string", length=150, nullable=false)
     */
    private $extra = '';


}
