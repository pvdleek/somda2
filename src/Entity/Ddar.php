<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_ddar", indexes={@ORM\Index(name="idx_47846_matid", columns={"matid"})})
 * @ORM\Entity
 */
class Ddar
{
    /**
     * @ORM\Column(name="id", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Train")
     * @ORM\JoinColumn(name="matid", referencedColumnName="matid")
     */
    public ?Train $train = null;

    /**
     * @ORM\Column(name="stam", type="smallint", nullable=true, options={"unsigned"=true})
     */
    public ?int $trunkNumber = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="afkid", referencedColumnName="afkid")
     */
    public ?Location $location = null;

    /**
     * @ORM\Column(name="spot_ander_laatste", type="date", nullable=true)
     */
    public ?\DateTime $timestampOtherLast = null;

    /**
     * @ORM\Column(name="spot_eerste", type="date", nullable=false)
     */
    public ?\DateTime $timestampFirst = null;

    /**
     * @ORM\Column(name="spot_laatste", type="date", nullable=true)
     */
    public ?\DateTime $timestampLast = null;

    /**
     * @ORM\Column(name="spot_ander_eerste", type="date", nullable=true)
     */
    public ?\DateTime $timestampOtherFirst = null;

    /**
     * @ORM\Column(name="extra", type="string", length=150, nullable=false)
     */
    public string $extra = '';
}
