<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="somda_trein",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_49046_treinnr", columns={"treinnr"})}
 * )
 * @ORM\Entity
 */
class Route extends Entity
{
    public const SPECIAL_NO_SERVICE = 'GDST';
    public const SPECIAL_EXTRA_SERVICE = ['LLT', 'LM', 'CARGO', 'REIZ', 'RG', 'WTR'];
    public const SPECIAL_MEASURING = 'MEET';
    public const SPECIAL_CHECKING = 'SCHOUW';

    /**
     * @var int
     * @ORM\Column(name="treinid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="treinnr", type="string", length=15, nullable=false)
     */
    public string $number = '';

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
     * @var Spot[]
     * @ORM\OneToMany(targetEntity="App\Entity\Spot", mappedBy="route")
     */
    private $spots;

    /**
     *
     */
    public function __construct()
    {
        $this->trainTables = new ArrayCollection();
        $this->trainTableFirstLasts = new ArrayCollection();
        $this->routeLists = new ArrayCollection();
        $this->spots = new ArrayCollection();
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
     * @param int $dayNumber
     * @return TrainTableFirstLast|null
     */
    public function getTrainTableFirstLastByDay(int $dayNumber): ?TrainTableFirstLast
    {
        foreach ($this->getTrainTableFirstLasts() as $trainTableFirstLast) {
            if ($trainTableFirstLast->dayNumber === $dayNumber) {
                return $trainTableFirstLast;
            }
        }
        return null;
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

    /**
     * @param Spot $spot
     * @return Route
     */
    public function addSpot(Spot $spot): Route
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
