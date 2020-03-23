<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_spot_provincie")
 * @ORM\Entity
 */
class PoiCategory
{
    /**
     * @var int
     * @ORM\Column(name="provincieid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="naam", type="string", length=15, nullable=false)
     */
    private $name = '';


    private $pois;

    public function __construct()
    {
        $this->pois = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return PoiCategory
     */
    public function setId(int $id): PoiCategory
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return PoiCategory
     */
    public function setName(string $name): PoiCategory
    {
        $this->name = $name;
        return $this;
    }
}
