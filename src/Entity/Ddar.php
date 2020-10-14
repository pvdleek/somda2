<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="dda_ddar", indexes={@ORM\Index(name="idx_47846_matid", columns={"matid"})})
 * @ORM\Entity
 */
class Ddar
{
    /**
     * @var int|null
     * @ORM\Column(name="dda_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var Train
     * @ORM\ManyToOne(targetEntity="App\Entity\Train")
     * @ORM\JoinColumn(name="matid", referencedColumnName="matid")
     */
    public Train $train;

    /**
     * @var int|null
     * @ORM\Column(name="dda_trunk_number", type="integer", nullable=true)
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
     * @ORM\Column(name="dda_timestamp_other_last", type="date", nullable=true)
     */
    public ?DateTime $timestampOtherLast;

    /**
     * @var DateTime
     * @ORM\Column(name="dda_timestamp_first", type="date", nullable=false)
     */
    public DateTime $timestampFirst;

    /**
     * @var DateTime|null
     * @ORM\Column(name="dda_timestamp_last", type="date", nullable=true)
     */
    public ?DateTime $timestampLast;

    /**
     * @var DateTime|null
     * @ORM\Column(name="dda_timestamp_other_first", type="date", nullable=true)
     */
    public ?DateTime $timestampOtherFirst;

    /**
     * @var string
     * @ORM\Column(name="dda_extra", type="string", length=150, nullable=false)
     */
    public string $extra = '';
}
