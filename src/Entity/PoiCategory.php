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
     * @var int|null
     * @ORM\Column(name="provincieid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="naam", type="string", length=15, nullable=false)
     */
    public string $name = '';

    /**
     * @var Poi[]
     */
    private $pois;

    /**
     *
     */
    public function __construct()
    {
        $this->pois = new ArrayCollection();
    }

    /**
     * @param Poi $poi
     * @return PoiCategory
     */
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
