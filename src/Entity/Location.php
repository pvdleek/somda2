<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(
 *     name="somda_verk",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="unq_somda_verk__afkorting_landid", columns={"afkorting", "landid"})},
 *     indexes={
 *         @ORM\Index(name="idx_somda_verk__landid", columns={"landid"}),
 *         @ORM\Index(name="idx_somda_verk__description", columns={"description"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\Location")
 */
class Location
{
    public const UNKNOWN_NAME = 'Fout!';

    /**
     * @ORM\Column(name="afkid", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="afkorting", type="string", length=10, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Abbreviation of the location", maxLength=10, type="string")
     */
    public string $name = '';

    /**
     * @ORM\Column(name="latitude", type="float", precision=10, scale=0, nullable=true)
     * @JMS\Expose()
     * @OA\Property(description="Latitude of the location", type="float")
     */
    public ?float $latitude = null;

    /**
     * @ORM\Column(name="longitude", type="float", precision=10, scale=0, nullable=true)
     * @JMS\Expose()
     * @OA\Property(description="Longitude of the location", type="float")
     */
    public ?float $longitude = null;

    /**
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Description of the location", maxLength=100, type="string")
     */
    public string $description = '';

    /**
     * @ORM\Column(name="traject", type="string", length=15, nullable=true)
     * @JMS\Expose()
     * @OA\Property(description="Route where this location is located", maxLength=15, type="string")
     */
    public ?string $routeDescription = null;

    /**
     * @ORM\Column(name="spot_allowed", type="boolean", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Is the location currently active (allowed to add spots)", type="boolean")
     */
    public bool $spotAllowed = true;

    /**
     * @ORM\Column(name="route_overstaptijd", type="smallint", nullable=true, options={"unsigned"=true})
     * @JMS\Exclude()
     */
    public ?int $transferTime = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LocationCategory", inversedBy="locations")
     * @ORM\JoinColumn(name="landid", referencedColumnName="verk_catid")
     * @JMS\Expose()
     * @OA\Property(
     *     description="The category to which this location belongs",
     *     ref=@Model(type=LocationCategory::class),
     * )
     */
    public LocationCategory $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrainTable", mappedBy="location")
     * @JMS\Exclude()
     */
    private $trainTables;

    /**
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
