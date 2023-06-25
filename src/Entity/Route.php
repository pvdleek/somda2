<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

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
     * @ORM\Column(name="treinid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="treinnr", type="string", length=15, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="The route-number", maxLength=15, type="string")
     */
    public string $number = '';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrainTable", mappedBy="route")
     * @JMS\Exclude()
     */
    private $trainTables;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrainTableFirstLast", mappedBy="route")
     * @JMS\Expose()
     * @OA\Property(description="The days on which this route runs", ref=@Model(type=TrainTableFirstLast::class))
     */
    private $trainTableFirstLasts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\RouteList", mappedBy="routes")
     * @JMS\Exclude()
     */
    private $routeLists;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Spot", mappedBy="route")
     * @JMS\Exclude()
     */
    private $spots;

    public function __construct()
    {
        $this->trainTables = new ArrayCollection();
        $this->trainTableFirstLasts = new ArrayCollection();
        $this->routeLists = new ArrayCollection();
        $this->spots = new ArrayCollection();
    }

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

    public function getTrainTableFirstLastByDay(int $trainTableYearId, int $dayNumber): ?TrainTableFirstLast
    {
        foreach ($this->getTrainTableFirstLasts() as $trainTableFirstLast) {
            if ($trainTableYearId === $trainTableFirstLast->trainTableYear->id
                && $dayNumber === $trainTableFirstLast->dayNumber
            ) {
                return $trainTableFirstLast;
            }
        }
        return null;
    }

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

    public function removeRouteList(RouteList $routeList): Route
    {
        $this->routeLists->removeElement($routeList);
        return $this;
    }

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
