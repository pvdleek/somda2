<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_spot_provincie')]
class PoiCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'provincieid', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'naam', length: 15, nullable: false, options: ['default' => ''])]
    public string $name = '';

    #[ORM\OneToMany(targetEntity: Poi::class, mappedBy: 'category')]
    private Collection $pois;

    public function __construct()
    {
        $this->pois = new ArrayCollection();
    }

    public function addPoi(Poi $poi): PoiCategory
    {
        $this->pois[] = $poi;
        return $this;
    }

    /**
     * @return Poi[]
     */
    public function getPois(): array
    {
        return $this->pois->toArray();
    }
}
