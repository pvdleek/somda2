<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(name="somda_tdr_drgl")
 * @ORM\Entity(repositoryClass="App\Repository\TrainTableYear")
 */
class TrainTableYear
{
    /**
     * @ORM\Column(name="tdr_nr", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="naam", type="string", length=10, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Name of the trainTableYear", maxLength=10, type="string")
     */
    public string $name = '';

    /**
     * @ORM\Column(name="start_datum", type="date", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="The start-date (00:00:00)")
     */
    public ?\DateTime $startDate = null;

    /**
     * @ORM\Column(name="eind_datum", type="date", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="The end-date (23:59:59)")
     */
    public ?\DateTime $endDate = null;

    /**
     * @throws \Exception
     * @JMS\VirtualProperty(name="active")
     * @OA\Property(description="Indication if the trainTableYear is currently active")
     */
    public function isActive(): bool
    {
        return $this->startDate <= new \DateTime() && $this->endDate >= new \DateTime();
    }
}
