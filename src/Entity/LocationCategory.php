<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_verk_cats")
 * @ORM\Entity
 */
class LocationCategory extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="verk_catid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    public $name = '';

    /**
     * @var Location[]
     * @ORM\OneToMany(targetEntity="App\Entity\Location", mappedBy="category")
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
