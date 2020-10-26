<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(
 *     name="loc_location",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="UNQ_loc_name_loa_id", columns={"loc_name", "loc_loa_id"})},
 *     indexes={
 *         @ORM\Index(name="IDX_loc_loa_id", columns={"loc_loa_id"}),
 *         @ORM\Index(name="IDX_loc_description", columns={"loc_description"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\Location")
 */
class Location
{
    public const UNKNOWN_NAME = 'Fout!';

    /**
     * @var int|null
     * @ORM\Column(name="loc_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="loc_name", type="string", length=10, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Abbreviation of the location", maxLength=10, type="string")
     */
    public string $name = '';

    /**
     * @var float|null
     * @ORM\Column(name="loc_latitude", type="float", precision=10, scale=0, nullable=true)
     * @JMS\Expose()
     * @SWG\Property(description="Latitude of the location", type="float")
     */
    public ?float $latitude;

    /**
     * @var float|null
     * @ORM\Column(name="loc_longitude", type="float", precision=10, scale=0, nullable=true)
     * @JMS\Expose()
     * @SWG\Property(description="Longitude of the location", type="float")
     */
    public ?float $longitude;

    /**
     * @var string
     * @ORM\Column(name="loc_description", type="string", length=100, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Description of the location", maxLength=100, type="string")
     */
    public string $description;

    /**
     * @var string|null
     * @ORM\Column(name="loc_route_description", type="string", length=15, nullable=true)
     * @JMS\Expose()
     * @SWG\Property(description="Route where this location is located", maxLength=15, type="string")
     */
    public ?string $routeDescription;

    /**
     * @var bool
     * @ORM\Column(name="loc_spot_allowed", type="boolean", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Is the location currently active (allowed to add spots)", type="boolean")
     */
    public bool $spotAllowed = true;

    /**
     * @var int|null
     * @ORM\Column(name="loc_tranfer_time", type="integer", nullable=true)
     * @JMS\Exclude()
     */
    public ?int $transferTime;

    /**
     * @var LocationCategory
     * @ORM\ManyToOne(targetEntity="App\Entity\LocationCategory", inversedBy="locations")
     * @ORM\JoinColumn(name="loc_loa_id", referencedColumnName="loa_id")
     * @JMS\Expose()
     * @SWG\Property(
     *     description="The category to which this location belongs",
     *     ref=@Model(type=LocationCategory::class),
     * )
     */
    public LocationCategory $category;

    /**
     * @var TrainTable[]
     * @ORM\OneToMany(targetEntity="App\Entity\TrainTable", mappedBy="location")
     * @JMS\Exclude()
     */
    private $trainTables;

    /**
     * @var Spot[]
     * @ORM\OneToMany(targetEntity="App\Entity\Spot", mappedBy="location")
     * @JMS\Exclude()
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
