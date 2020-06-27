<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="somda_verk_cats")
 * @ORM\Entity
 */
class LocationCategory extends Entity
{
    public const NO_LONGER_VALID_ID = 50;

    /**
     * @var int
     * @ORM\Column(name="verk_catid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     */
    protected ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="code", type="string", length=5, nullable=false)
     * @JMS\Exclude()
     */
    public string $code = '';

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     * @JMS\Expose()
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
