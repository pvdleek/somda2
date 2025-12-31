<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
#[ORM\Table(
    name: 'somda_verk',
    uniqueConstraints: [new ORM\UniqueConstraint(name: 'unq_somda_verk__afkorting_landid', columns: ['afkorting', 'landid'])],
    indexes: [new ORM\Index(name: 'idx_somda_verk__landid', columns: ['landid']), new ORM\Index(name: 'idx_somda_verk__description', columns: ['description'])]
)]
class Location
{
    public const UNKNOWN_NAME = 'Fout!';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'afkid', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'afkorting', length: 10, nullable: false, options: ['default' => ''])]
    public string $name = '';

    #[ORM\Column(precision: 10, scale: 0, nullable: true)]
    public ?float $latitude = null;

    #[ORM\Column(precision: 10, scale: 0, nullable: true)]
    public ?float $longitude = null;

    #[ORM\Column(length: 100, nullable: false, options: ['default' => ''])]
    public string $description = '';

    #[ORM\Column(nullable: false, options: ['default' => true])]
    public bool $spot_allowed = true;

    #[ORM\ManyToOne(targetEntity: LocationCategory::class, inversedBy: 'locations')]
    #[ORM\JoinColumn(name: 'landid', referencedColumnName: 'verk_catid')]
    public LocationCategory $category;

    #[ORM\OneToMany(targetEntity: TrainTable::class, mappedBy: 'location')]
    private Collection $train_tables;

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
