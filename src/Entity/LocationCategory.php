<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(name="loa_location_category")
 * @ORM\Entity
 */
class LocationCategory
{
    public const NO_LONGER_VALID_ID = 50;

    /**
     * @var int|null
     * @ORM\Column(name="loa_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="loa_code", type="string", length=5, nullable=false)
     * @JMS\Exclude()
     */
    public string $code = '';

    /**
     * @var string
     * @ORM\Column(name="loa_name", type="string", length=20, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Name of the location-category", maxLength=20, type="string")
     */
    public string $name = '';

    /**
     * @var Location[]
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
