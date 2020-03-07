<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaDrglLogging
 *
 * @ORM\Table(name="somda_drgl_logging")
 * @ORM\Entity
 */
class SomdaDrglLogging
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
     * @ORM\Column(name="drglid", type="bigint", nullable=false)
     */
    private $drglid;

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

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tijd", type="time", nullable=false)
     */
    private $tijd;

    /**
     * @var string
     *
     * @ORM\Column(name="actie", type="text", length=0, nullable=false)
     */
    private $actie;


}
