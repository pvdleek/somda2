<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(
 *     name="somda_trein",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_49046_treinnr", columns={"treinnr"})}
 * )
 * @ORM\Entity
 */
class Route
{
    public const SPECIAL_NO_SERVICE = 'GDST';
    public const SPECIAL_EXTRA_SERVICE = ['LLT', 'LM', 'CARGO', 'REIZ', 'RG', 'WTR'];
    public const SPECIAL_MEASURING = 'MEET';
    public const SPECIAL_CHECKING = 'SCHOUW';

    /**
     * @var int|null
     * @ORM\Column(name="treinid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="treinnr", type="string", length=15, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="The route-number", maxLength=15, type="string")
     */
    public string $number = '';

    /**
     * @var TrainTable[]
     * @ORM\OneToMany(targetEntity="App\Entity\TrainTable", mappedBy="route")
     * @JMS\Exclude()
     */
    private $trainTables;

    /**
     * @var TrainTableFirstLast[]
     * @ORM\OneToMany(targetEntity="App\Entity\TrainTableFirstLast", mappedBy="route")
     * @JMS\Expose()
     * @SWG\Property(description="The days on which this route runs", ref=@Model(type=TrainTableFirstLast::class))
     */
    private $trainTableFirstLasts;

    /**
     * @var RouteList
     * @ORM\ManyToMany(targetEntity="App\Entity\RouteList", mappedBy="routes")
     * @JMS\Exclude()
     */
    private $routeLists;

    /**
     * @var Spot[]
     * @ORM\OneToMany(targetEntity="App\Entity\Spot", mappedBy="route")
     * @JMS\Exclude()
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
     * @param RouteList $routeList
     * @return $this
     */
    public function removeRouteList(RouteList $routeList): Route
    {
        $this->routeLists->removeElement($routeList);
        return $this;
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
