<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
#[ORM\Table(
    name: 'somda_verk',
    uniqueConstraints: [new ORM\UniqueConstraint(name: 'unq_somda_verk__afkorting_landid', columns: ['afkorting', 'landid'])],
    indexes: [new ORM\Index(name: 'idx_somda_verk__landid', columns: ['landid']), new ORM\Index(name: 'idx_somda_verk__description', columns: ['description'])]
)]
class Location
{
    public const UNKNOWN_NAME = 'Fout!';

    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'afkid', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Abbreviation of the location", maxLength=10, type="string")
     */
    #[ORM\Column(name: 'afkorting', length: 10, nullable: false, options: ['default' => ''])]
    public string $name = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="Latitude of the location", type="float")
     */
    #[ORM\Column(precision: 10, scale: 0, nullable: true)]
    public ?float $latitude = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Longitude of the location", type="float")
     */
    #[ORM\Column(precision: 10, scale: 0, nullable: true)]
    public ?float $longitude = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Description of the location", maxLength=100, type="string")
     */
    #[ORM\Column(length: 100, nullable: false, options: ['default' => ''])]
    public string $description = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="Is the location currently active (allowed to add spots)", type="boolean")
     */
    #[ORM\Column(nullable: false, options: ['default' => true])]
    public bool $spot_allowed = true;

    /**
     * @JMS\Expose()
     * @OA\Property(
     *     description="The category to which this location belongs",
     *     ref=@Model(type=LocationCategory::class),
     * )
     */
    #[ORM\ManyToOne(targetEntity: LocationCategory::class, inversedBy: 'locations')]
    #[ORM\JoinColumn(name: 'landid', referencedColumnName: 'verk_catid')]
    public LocationCategory $category;

    /**
     * @JMS\Exclude()
     */
    #[ORM\OneToMany(targetEntity: TrainTable::class, mappedBy: 'location')]
    private Collection $train_tables;

    /**
     * @JMS\Exclude()
     */
    #[ORM\OneToMany(targetEntity: Spot::class, mappedBy: 'location')]
    private Collection $spots;

    /**
     *
     */
    public function __construct()
    {
        $this->train_tables = new ArrayCollection();
        $this->spots = new ArrayCollection();
    }

    public function addTrainTable(TrainTable $trainTable): Location
    {
        $this->train_tables[] = $trainTable;
        return $this;
    }

    /**
     * @return TrainTable[]
     */
    public function getTrainTables(): array
    {
        return $this->train_tables->toArray();
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
