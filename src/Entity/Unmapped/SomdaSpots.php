<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaSpots
 *
 * @ORM\Table(name="somda_spots", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48259_treinid", columns={"treinid", "posid", "locatieid", "matid", "uid", "datum"})}, indexes={@ORM\Index(name="idx_48259_matid", columns={"matid"}), @ORM\Index(name="idx_48259_datum", columns={"datum"}), @ORM\Index(name="idx_48259_uid", columns={"uid"})})
 * @ORM\Entity
 */
class SomdaSpots
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
     * @var int|null
     *
     * @ORM\Column(name="in_spotid", type="bigint", nullable=true)
     */
    private $inSpotid;

    /**
     * @var int
     *
     * @ORM\Column(name="treinid", type="bigint", nullable=false)
     */
    private $treinid;

    /**
     * @var int
     *
     * @ORM\Column(name="posid", type="bigint", nullable=false)
     */
    private $posid;

    /**
     * @var int
     *
     * @ORM\Column(name="locatieid", type="bigint", nullable=false)
     */
    private $locatieid;

    /**
     * @var int
     *
     * @ORM\Column(name="matid", type="bigint", nullable=false)
     */
    private $matid;

    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     */
    private $uid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datum", type="date", nullable=false)
     */
    private $datum;


}
