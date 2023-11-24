<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_spot_provincie")
 * @ORM\Entity
 */
class PoiCategory
{
    /**
     * @ORM\Column(name="provincieid", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="naam", type="string", length=15, nullable=false)
     */
    public string $name = '';

    private $pois;

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
