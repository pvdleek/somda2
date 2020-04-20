<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mat", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48117_nummer", columns={"nummer"})}, indexes={@ORM\Index(name="idx_48117_vervoerder_id", columns={"vervoerder_id"})})
 * @ORM\Entity
 */
class Train extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="matid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="nummer", type="string", length=20, nullable=false)
     */
    public $number = '';

    /**
     * @var string|null
     * @ORM\Column(name="naam", type="string", length=35, nullable=true)
     */
    public $name;

    /**
     * @var Transporter
     * @ORM\ManyToOne(targetEntity="App\Entity\Transporter")
     * @ORM\JoinColumn(name="vervoerder_id", referencedColumnName="vervoerder_id")
     */
    public $transporter;

    /**
     * @var TrainNamePattern|null
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainNamePattern")
     * @ORM\JoinColumn(name="pattern_id", referencedColumnName="id")
     */
    public $namePattern;

    /**
     * @var Spot[]
     * @ORM\OneToMany(targetEntity="App\Entity\Spot", mappedBy="train")
     */
    private $spots;

    /**
     *
     */
    public function __construct()
    {
        $this->spots = new ArrayCollection();
    }

    /**
     * @param Spot $spot
     * @return Train
     */
    public function addSpot(Spot $spot): Train
    {
        $this->spots[] = $spot;
        return $this;
    }

    /**
     * @return Spot[]
     */
    public function getSpots(): array
    {
        return $this->spots->toArray();
    }
}
