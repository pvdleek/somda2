<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_vervoerder')]
#[ORM\UniqueConstraint(name: 'unq_somda_vervoerder__omschrijving', columns: ['omschrijving'])]
class Transporter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'vervoerder_id', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'omschrijving', length: 50, nullable: false, options: ['default' => ''])]
    public string $name = '';

    #[ORM\Column(name: 'iff_code', nullable: true, options: ['unsigned' => true])]
    public ?int $iff_code = null;

    #[ORM\OneToMany(targetEntity: Train::class, mappedBy: 'transporter')]
    private Collection $trains;

    #[ORM\OneToMany(targetEntity: RouteList::class, mappedBy: 'transporter')]
    private Collection $route_lists;

    public function __construct()
    {
        $this->trains = new ArrayCollection();
        $this->route_lists = new ArrayCollection();
    }

    public function getIffCode(): ?int
    {
        return $this->iff_code;
    }

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

    public function addRouteList(RouteList $route_list): Transporter
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
}
