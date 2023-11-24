<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(name="somda_verk_cats")
 * @ORM\Entity
 */
class LocationCategory
{
    public const NO_LONGER_VALID_ID = 50;

    /**
     * @ORM\Column(name="verk_catid", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="code", type="string", length=5, nullable=false)
     * @JMS\Exclude()
     */
    public string $code = '';

    /**
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Name of the location-category", maxLength=20, type="string")
     */
    public string $name = '';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Location", mappedBy="category")
     * @JMS\Exclude()
     */
    private $locations;

    /**
     *
     */
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
