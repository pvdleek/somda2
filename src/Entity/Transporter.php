<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

#[ORM\Entity]
#[ORM\Table(name: 'somda_vervoerder', uniqueConstraints: [new ORM\UniqueConstraint(name: 'unq_somda_vervoerder__omschrijving', columns: ['omschrijving'])])]
class Transporter
{
    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'vervoerder_id', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Name of the transporter", maxLength=50, type="string")
     */
    #[ORM\Column(name: 'omschrijving', length: 50, nullable: false, options: ['default' => ''])]
    public string $name = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="Official IFF code", type="integer")
     */
    #[ORM\Column(name: 'iff_code', nullable: true, options: ['unsigned' => true])]
    public ?int $iffCode = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\OneToMany(targetEntity: Train::class, mappedBy: 'transporter')]
    private Collection $trains;

    /**
     * @JMS\Exclude()
     */
    #[ORM\OneToMany(targetEntity: RouteList::class, mappedBy: 'transporter')]
    private Collection $route_lists;

    public function __construct()
    {
        $this->trains = new ArrayCollection();
        $this->route_lists = new ArrayCollection();
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
