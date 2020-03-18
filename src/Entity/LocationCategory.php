<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_verk_cats")
 * @ORM\Entity
 */
class LocationCategory
{
    /**
     * @var int
     * @ORM\Column(name="verk_catid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    private $name = '';

    /**
     * @var string|null
     * @ORM\Column(name="ns_code", type="string", length=2, nullable=true)
     */
    private $nsCode;

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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return LocationCategory
     */
    public function setId(int $id): LocationCategory
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
     * @return LocationCategory
     */
    public function setName(string $name): LocationCategory
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNsCode(): ?string
    {
        return $this->nsCode;
    }

    /**
     * @param string|null $nsCode
     * @return LocationCategory
     */
    public function setNsCode(?string $nsCode): LocationCategory
    {
        $this->nsCode = $nsCode;
        return $this;
    }
}
