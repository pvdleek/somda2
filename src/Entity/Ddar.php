<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_ddar", indexes={@ORM\Index(name="idx_47846_matid", columns={"matid"})})
 * @ORM\Entity
 */
class Ddar extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var Train
     * @ORM\ManyToOne(targetEntity="App\Entity\Train")
     * @ORM\JoinColumn(name="matid", referencedColumnName="matid")
     */
    public Train $train;

    /**
     * @var int|null
     * @ORM\Column(name="stam", type="integer", nullable=true)
     */
    public ?int $trunkNumber;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="afkid", referencedColumnName="afkid")
     */
    public Location $location;

    /**
     * @var DateTime|null
     * @ORM\Column(name="spot_ander_laatste", type="date", nullable=true)
     */
    public ?DateTime $spotTimestampOtherLast;

    /**
     * @var DateTime
     * @ORM\Column(name="spot_eerste", type="date", nullable=false)
     */
    public DateTime $spotTimestampFirst;

    /**
     * @var DateTime|null
     * @ORM\Column(name="spot_laatste", type="date", nullable=true)
     */
    public ?DateTime $spotTimestampLast;

    /**
     * @var DateTime|null
     * @ORM\Column(name="spot_ander_eerste", type="date", nullable=true)
     */
    public ?DateTime $spotTimestampOtherFirst;

    /**
     * @var string
     * @ORM\Column(name="extra", type="string", length=150, nullable=false)
     */
    public string $extra = '';
}
