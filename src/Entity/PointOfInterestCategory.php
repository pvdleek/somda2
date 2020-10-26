<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="pon_point_of_interest_category")
 * @ORM\Entity
 */
class PointOfInterestCategory
{
    /**
     * @var int|null
     * @ORM\Column(name="pon_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="pon_name", type="string", length=15, nullable=false)
     */
    public string $name = '';

    /**
     * @var PointOfInterest[]
     */
    private $points;

    /**
     *
     */
    public function __construct()
    {
        $this->points = new ArrayCollection();
    }

    /**
     * @param PointOfInterest $poi
     * @return PointOfInterestCategory
     */
    public function addPoint(PointOfInterest $poi): PointOfInterestCategory
    {
        $this->points[] = $poi;
        return $this;
    }

    /**
     * @return PointOfInterest[]
     */
    public function getPoints(): array
    {
        return $this->points->toArray();
    }
}
