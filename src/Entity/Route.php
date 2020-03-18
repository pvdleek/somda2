<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_trein", uniqueConstraints={@ORM\UniqueConstraint(name="idx_49046_treinnr", columns={"treinnr"})})
 * @ORM\Entity
 */
class Route
{
    /**
     * @var int
     * @ORM\Column(name="treinid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="treinnr", type="string", length=15, nullable=false)
     */
    private $number = '';

    /**
     * @var TrainTable[]
     * @ORM\OneToMany(targetEntity="App\Entity\TrainTable", mappedBy="route")
     */
    private $trainTables;

    /**
     * @var TrainTableFirstLast[]
     * @ORM\OneToMany(targetEntity="App\Entity\TrainTableFirstLast", mappedBy="route")
     */
    private $trainTableFirstLasts;

    /**
     * @var RouteList
     * @ORM\ManyToMany(targetEntity="App\Entity\RouteList", mappedBy="routes")
     */
    private $routeLists;

    /**
     *
     */
    public function __construct()
    {
        $this->trainTables = new ArrayCollection();
        $this->trainTableFirstLasts = new ArrayCollection();
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
     * @return Route
     */
    public function setId(int $id): Route
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     * @return Route
     */
    public function setNumber(string $number): Route
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @param TrainTable $trainTable
     * @return Route
     */
    public function addTrainTable(TrainTable $trainTable): Route
    {
        $this->trainTables[] = $trainTable;
        return $this;
    }

    /**
     * @return TrainTable[]
     */
    public function getTrainTables(): array
    {
        return $this->trainTables->toArray();
    }

    /**
     * @param TrainTableFirstLast $trainTableFirstLast
     * @return Route
     */
    public function addTrainTableFirstLast(TrainTableFirstLast $trainTableFirstLast): Route
    {
        $this->trainTableFirstLasts[] = $trainTableFirstLast;
        return $this;
    }

    /**
     * @return TrainTableFirstLast[]
     */
    public function getTrainTableFirstLasts(): array
    {
        return $this->trainTableFirstLasts->toArray();
    }

    /**
     * @param RouteList $routeList
     * @return Route
     */
    public function addRouteList(RouteList $routeList): Route
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
