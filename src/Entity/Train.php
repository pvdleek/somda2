<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mat", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48117_nummer", columns={"nummer"})}, indexes={@ORM\Index(name="idx_48117_vervoerder_id", columns={"vervoerder_id"})})
 * @ORM\Entity
 */
class Train
{
    /**
     * @var int
     * @ORM\Column(name="matid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="nummer", type="string", length=20, nullable=false)
     */
    private $number = '';

    /**
     * @var string|null
     * @ORM\Column(name="naam", type="string", length=35, nullable=true)
     */
    private $name;

    /**
     * @var Transporter
     * @ORM\ManyToOne(targetEntity="App\Entity\Transporter")
     * @ORM\JoinColumn(name="vervoerder_id", referencedColumnName="vervoerder_id")
     */
    private $transporter;

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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Train
     */
    public function setId(int $id): Train
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     * @return Train
     */
    public function setNumber(string $number): Train
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Train
     */
    public function setName(?string $name): Train
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Transporter
     */
    public function getTransporter(): Transporter
    {
        return $this->transporter;
    }

    /**
     * @param Transporter $transporter
     * @return Train
     */
    public function setTransporter(Transporter $transporter): Train
    {
        $this->transporter = $transporter;
        return $this;
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
