<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(name="somda_rijdagen")
 * @ORM\Entity(repositoryClass="App\Repository\RouteOperationDays")
 */
class RouteOperationDays extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="rijdagenid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier, binary representation of the operation days", type="integer")
     */
    protected ?int $id = null;

    /**
     * @var bool
     * @ORM\Column(name="ma", type="boolean", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Is the route operating on Monday", type="boolean")
     */
    public bool $monday = false;

    /**
     * @var bool
     * @ORM\Column(name="di", type="boolean", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Is the route operating on Tuesday", type="boolean")
     */
    public bool $tuesday = false;

    /**
     * @var bool
     * @ORM\Column(name="wo", type="boolean", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Is the route operating on Wednesday", type="boolean")
     */
    public bool $wednesday = false;

    /**
     * @var bool
     * @ORM\Column(name="do", type="boolean", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Is the route operating on Thursday", type="boolean")
     */
    public bool $thursday = false;

    /**
     * @var bool
     * @ORM\Column(name="vr", type="boolean", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Is the route operating on Friday", type="boolean")
     */
    public bool $friday = false;

    /**
     * @var bool
     * @ORM\Column(name="za", type="boolean", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Is the route operating on Saturday", type="boolean")
     */
    public bool $saturday = false;

    /**
     * @var bool
     * @ORM\Column(name="zf", type="boolean", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Is the route operating on Sunday", type="boolean")
     */
    public bool $sunday = false;

    /**
     * @param int $dayNumber
     * @return bool
     */
    public function isRunningOnDay(int $dayNumber): bool
    {
        switch ($dayNumber) {
            case 0:
                $day = $this->monday;
                break;
            case 1:
                $day = $this->tuesday;
                break;
            case 2:
                $day = $this->wednesday;
                break;
            case 3:
                $day = $this->thursday;
                break;
            case 4:
                $day = $this->friday;
                break;
            case 5:
                $day = $this->saturday;
                break;
            default:
                $day = $this->sunday;
                break;
        }
        return $day;
    }
}
