<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

#[ORM\Entity]
#[ORM\Table(name: 'somda_verk_cats')]
class LocationCategory
{
    public const NO_LONGER_VALID_ID = 50;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'verk_catid', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\Column(length: 5, nullable: false, options: ['default' => ''])]
    public string $code = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="Name of the location-category", maxLength=20, type="string")
     */
    #[ORM\Column(length: 20, nullable: false, options: ['default' => ''])]
    public string $name = '';

    /**
     * @JMS\Exclude()
     */
    #[ORM\OneToMany(targetEntity: Location::class, mappedBy: 'category')]
    private Collection $locations;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
    }

    /**
     * @param Location $location
     * @return LocationCategory
     */
    public function addLocation(Location $location): LocationCategory
    {
        $this->locations[] = $location;
        return $this;
    }

    /**
     * @return Location[]
     */
    public function getLocations(): array
    {
        return $this->locations->toArray();
    }
}
