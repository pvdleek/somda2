<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_vervoerder", uniqueConstraints={@ORM\UniqueConstraint(name="idx_49122_omschrijving", columns={"omschrijving"})})
 * @ORM\Entity
 */
class Transporter
{
    /**
     * @var int
     * @ORM\Column(name="vervoerder_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="omschrijving", type="string", length=35, nullable=false)
     */
    private $name = '';

    /**
     * @var string|null
     * @ORM\Column(name="prorail_desc", type="string", length=35, nullable=true)
     */
    private $proRailDescription = '';

    /**
     * @var Train[]
     */
    private $trains;

    /**
     * @var RouteList[]
     */
    private $routeLists;

    /**
     *
     */
    public function __construct()
    {
        $this->trains = new ArrayCollection();
        $this->routeLists = new ArrayCollection();
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
     * @return Transporter
     */
    public function setId(int $id): Transporter
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
     * @return Transporter
     */
    public function setName(string $name): Transporter
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProRailDescription(): ?string
    {
        return $this->proRailDescription;
    }

    /**
     * @param string|null $proRailDescription
     * @return Transporter
     */
    public function setProRailDescription(?string $proRailDescription): Transporter
    {
        $this->proRailDescription = $proRailDescription;
        return $this;
    }

    /**
     * @param Train $train
     * @return Transporter
     */
    public function addTrain(Train $train): Transporter
    {
        $this->trains[] = $train;
        return $this;
    }

    /**
     * @return Train[]
     */
    public function getTrains(): array
    {
        return $this->trains->toArray();
    }

    /**
     * @param RouteList $routeList
     * @return Transporter
     */
    public function addRouteList(RouteList $routeList): Transporter
    {
        $this->routeLists[] = $routeList;
        return $this;
    }

    /**
     * @return RouteList[]
     */
    public function getRouteLists(): array
    {
        return $this->routeLists->toArray();
    }
}
