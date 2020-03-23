<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_verk", uniqueConstraints={@ORM\UniqueConstraint(name="idx_49103_afkorting_2", columns={"afkorting", "landid"})}, indexes={@ORM\Index(name="idx_49103_landid", columns={"landid"}), @ORM\Index(name="idx_49103_description", columns={"description"})})
 * @ORM\Entity(repositoryClass="App\Repository\Location")
 */
class Location
{
    /**
     * @var int
     * @ORM\Column(name="afkid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="afkorting", type="string", length=10, nullable=false)
     */
    private $name = '';

    /**
     * @var float|null
     * @ORM\Column(name="latitude", type="float", precision=10, scale=0, nullable=true)
     */
    private $latitude;

    /**
     * @var float|null
     * @ORM\Column(name="longitude", type="float", precision=10, scale=0, nullable=true)
     */
    private $longitude;

    /**
     * @var int
     * @ORM\Column(name="hafas_code", type="bigint", nullable=true)
     */
    private $hafasCode;

    /**
     * @var string|null
     * @ORM\Column(name="hafas_desc", type="string", length=50, nullable=true)
     */
    private $hafasDescription;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     */
    private $description;

    /**
     * @var string|null
     * @ORM\Column(name="traject", type="string", length=15, nullable=true)
     */
    private $routeDescription;

    /**
     * @var boolean
     * @ORM\Column(name="spot_allowed", type="boolean", nullable=false)
     */
    private $spotAllowed = true;

    /**
     * @var int|null
     * @ORM\Column(name="route_overstaptijd", type="bigint", nullable=true)
     */
    private $transferTime;

    /**
     * @var LocationCategory
     * @ORM\ManyToOne(targetEntity="App\Entity\LocationCategory", inversedBy="locations")
     * @ORM\JoinColumn(name="landid", referencedColumnName="verk_catid")
     */
    private $category;

    /**
     * @var TrainTable[]
     * @ORM\OneToMany(targetEntity="App\Entity\TrainTable", mappedBy="location")
     */
    private $trainTables;

    /**
     * @var Spot[]
     * @ORM\OneToMany(targetEntity="App\Entity\Spot", mappedBy="location")
     */
    private $spots;

    /**
     *
     */
    public function __construct()
    {
        $this->trainTables = new ArrayCollection();
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
     * @return Location
     */
    public function setId(int $id): Location
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Location
     */
    public function setName(string $name): Location
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param float|null $latitude
     * @return Location
     */
    public function setLatitude(?float $latitude): Location
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float|null $longitude
     * @return Location
     */
    public function setLongitude(?float $longitude): Location
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * @return int
     */
    public function getHafasCode(): int
    {
        return $this->hafasCode;
    }

    /**
     * @param int $hafasCode
     * @return Location
     */
    public function setHafasCode(int $hafasCode): Location
    {
        $this->hafasCode = $hafasCode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHafasDescription(): ?string
    {
        return $this->hafasDescription;
    }

    /**
     * @param string|null $hafasDescription
     * @return Location
     */
    public function setHafasDescription(?string $hafasDescription): Location
    {
        $this->hafasDescription = $hafasDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Location
     */
    public function setDescription(string $description): Location
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRouteDescription(): ?string
    {
        return $this->routeDescription;
    }

    /**
     * @param string|null $routeDescription
     * @return Location
     */
    public function setRouteDescription(?string $routeDescription): Location
    {
        $this->routeDescription = $routeDescription;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSpotAllowed(): bool
    {
        return $this->spotAllowed;
    }

    /**
     * @param bool $spotAllowed
     * @return Location
     */
    public function setSpotAllowed(bool $spotAllowed): Location
    {
        $this->spotAllowed = $spotAllowed;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTransferTime(): ?int
    {
        return $this->transferTime;
    }

    /**
     * @param int|null $transferTime
     * @return Location
     */
    public function setTransferTime(?int $transferTime): Location
    {
        $this->transferTime = $transferTime;
        return $this;
    }

    /**
     * @return LocationCategory
     */
    public function getCategory(): LocationCategory
    {
        return $this->category;
    }

    /**
     * @param LocationCategory $category
     * @return Location
     */
    public function setCategory(LocationCategory $category): Location
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @param TrainTable $trainTable
     * @return Location
     */
    public function addTrainTable(TrainTable $trainTable): Location
    {
        $this->trainTables[] = $trainTable;
        return $this;
    }

    /**
     * @return TrainTable[]
     */
    public function getTrainTables(): array
    {
        return $this->trainTables->toArray();
    }

    /**
     * @param Spot $spot
     * @return Location
     */
    public function addSpot(Spot $spot): Location
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
