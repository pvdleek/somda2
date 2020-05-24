<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="somda_verk",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_49103_afkorting_2", columns={"afkorting", "landid"})},
 *     indexes={
 *         @ORM\Index(name="idx_49103_landid", columns={"landid"}),
 *         @ORM\Index(name="idx_49103_description", columns={"description"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\Location")
 */
class Location extends Entity
{
    public const UNKNOWN_NAME = 'Fout!';

    /**
     * @var int
     * @ORM\Column(name="afkid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="afkorting", type="string", length=10, nullable=false)
     */
    public string $name = '';

    /**
     * @var float|null
     * @ORM\Column(name="latitude", type="float", precision=10, scale=0, nullable=true)
     */
    public ?float $latitude;

    /**
     * @var float|null
     * @ORM\Column(name="longitude", type="float", precision=10, scale=0, nullable=true)
     */
    public ?float $longitude;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     */
    public string $description;

    /**
     * @var string|null
     * @ORM\Column(name="traject", type="string", length=15, nullable=true)
     */
    public ?string $routeDescription;

    /**
     * @var bool
     * @ORM\Column(name="spot_allowed", type="boolean", nullable=false)
     */
    public bool $spotAllowed = true;

    /**
     * @var int|null
     * @ORM\Column(name="route_overstaptijd", type="integer", nullable=true)
     */
    public ?int $transferTime;

    /**
     * @var LocationCategory
     * @ORM\ManyToOne(targetEntity="App\Entity\LocationCategory", inversedBy="locations")
     * @ORM\JoinColumn(name="landid", referencedColumnName="verk_catid")
     */
    public LocationCategory $category;

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
