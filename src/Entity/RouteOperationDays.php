<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(name="somda_rijdagen")
 * @ORM\Entity(repositoryClass="App\Repository\RouteOperationDays")
 */
class RouteOperationDays
{
    /**
     * @ORM\Column(name="rijdagenid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier, binary representation of the operation days", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="ma", type="boolean", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Is the route operating on Monday", type="boolean")
     */
    public bool $monday = false;

    /**
     * @ORM\Column(name="di", type="boolean", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Is the route operating on Tuesday", type="boolean")
     */
    public bool $tuesday = false;

    /**
     * @ORM\Column(name="wo", type="boolean", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Is the route operating on Wednesday", type="boolean")
     */
    public bool $wednesday = false;

    /**
     * @ORM\Column(name="do", type="boolean", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Is the route operating on Thursday", type="boolean")
     */
    public bool $thursday = false;

    /**
     * @ORM\Column(name="vr", type="boolean", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Is the route operating on Friday", type="boolean")
     */
    public bool $friday = false;

    /**
     * @ORM\Column(name="za", type="boolean", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Is the route operating on Saturday", type="boolean")
     */
    public bool $saturday = false;

    /**
     * @ORM\Column(name="zf", type="boolean", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Is the route operating on Sunday", type="boolean")
     */
    public bool $sunday = false;

    public function isRunningOnDay(int $dayNumber): bool
    {
        switch ($dayNumber) {
            case 0:
                return $this->monday;
            case 1:
                return $this->tuesday;
            case 2:
                return $this->wednesday;
            case 3:
                return $this->thursday;
            case 4:
                return $this->friday;
            case 5:
                return $this->saturday;
        }
        return $this->sunday;
    }
}
