<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RouteOperationDaysRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

#[ORM\Entity(repositoryClass: RouteOperationDaysRepository::class)]
#[ORM\Table(name: 'somda_rijdagen')]
class RouteOperationDays
{
    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier, binary representation of the operation days", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'rijdagenid', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Is the route operating on Monday", type="boolean")
     */
    #[ORM\Column(name: 'ma', nullable: false, options: ['default' => false])]
    public bool $monday = false;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Is the route operating on Tuesday", type="boolean")
     */
    #[ORM\Column(name: 'di', nullable: false, options: ['default' => false])]
    public bool $tuesday = false;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Is the route operating on Wednesday", type="boolean")
     */
    #[ORM\Column(name: 'wo', nullable: false, options: ['default' => false])]
    public bool $wednesday = false;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Is the route operating on Thursday", type="boolean")
     */
    #[ORM\Column(name: 'do', nullable: false, options: ['default' => false])]
    public bool $thursday = false;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Is the route operating on Friday", type="boolean")
     */
    #[ORM\Column(name: 'vr', nullable: false, options: ['default' => false])]
    public bool $friday = false;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Is the route operating on Saturday", type="boolean")
     */
    #[ORM\Column(name: 'za', nullable: false, options: ['default' => false])]
    public bool $saturday = false;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Is the route operating on Sunday", type="boolean")
     */
    #[ORM\Column(name: 'zf', nullable: false, options: ['default' => false])]
    public bool $sunday = false;

    public function isRunningOnDay(int $day_number): bool
    {
        switch ($day_number) {
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
