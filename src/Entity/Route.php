<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_trein')]
#[ORM\UniqueConstraint(name: 'unq_somda_trein__treinnr', columns: ['treinnr'])]
class Route
{
    public const SPECIAL_NO_SERVICE = 'GDST';
    public const SPECIAL_EXTRA_SERVICE = ['LLT', 'LM', 'CARGO', 'REIZ', 'RG', 'WTR'];
    public const SPECIAL_MEASURING = 'MEET';
    public const SPECIAL_CHECKING = 'SCHOUW';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'treinid', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'treinnr', length: 25, nullable: false, options: ['default' => ''])]
    public string $number = '';

    #[ORM\OneToMany(targetEntity: TrainTable::class, mappedBy: 'route')]
    private Collection $train_tables;

    #[ORM\OneToMany(targetEntity: TrainTableFirstLast::class, mappedBy: 'route')]
    private Collection $train_table_first_lasts;

    #[ORM\ManyToMany(targetEntity: RouteList::class, mappedBy: 'routes')]
    private Collection $route_lists;

    #[ORM\OneToMany(targetEntity: Spot::class, mappedBy: 'route')]
    private Collection $spots;

    public function __construct()
    {
        $this->train_tables = new ArrayCollection();
        $this->train_table_first_lasts = new ArrayCollection();
        $this->route_lists = new ArrayCollection();
        $this->spots = new ArrayCollection();
    }

    public function addTrainTable(TrainTable $train_table): Route
    {
        $this->train_tables[] = $train_table;
        
        return $this;
    }

    /**
     * @return TrainTable[]
     */
    public function getTrainTables(): array
    {
        return $this->train_tables->toArray();
    }

    public function addTrainTableFirstLast(TrainTableFirstLast $train_table_first_last): Route
    {
        $this->train_table_first_lasts[] = $train_table_first_last;

        return $this;
    }

    /**
     * @return TrainTableFirstLast[]
     */
    public function getTrainTableFirstLasts(): array
    {
        return $this->train_table_first_lasts->toArray();
    }

    public function getTrainTableFirstLastByDay(int $train_table_year_id, int $day_number): ?TrainTableFirstLast
    {
        foreach ($this->getTrainTableFirstLasts() as $train_table_first_last) {
            if ($train_table_year_id === $train_table_first_last->train_table_year->id
                && $day_number === $train_table_first_last->day_number
            ) {
                return $train_table_first_last;
            }
        }
        
        return null;
    }

    public function addRouteList(RouteList $route_list): Route
    {
        $this->route_lists[] = $route_list;

        return $this;
    }

    /**
     * @return RouteList[]
     */
    public function getRouteLists(): array
    {
        return $this->route_lists->toArray();
    }

    public function removeRouteList(RouteList $route_list): Route
    {
        $this->route_lists->removeElement($route_list);

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
