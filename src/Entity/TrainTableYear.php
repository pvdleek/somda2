<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TrainTableYearRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

#[ORM\Entity(repositoryClass: TrainTableYearRepository::class)]
#[ORM\Table(name: 'somda_tdr_drgl')]
class TrainTableYear
{
    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'tdr_nr', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Name of the trainTableYear", maxLength=10, type="string")
     */
    #[ORM\Column(name: 'naam', length: 10, nullable: false, options: ['default' => ''])]
    public string $name = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="The start-date (00:00:00)")
     */
    #[ORM\Column(name: 'start_datum', type: 'date', nullable: true)]
    public ?\DateTime $start_date = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The end-date (23:59:59)")
     */
    #[ORM\Column(name: 'eind_datum', type: 'date', nullable: true)]
    public ?\DateTime $end_date = null;

    /**
     * @throws \Exception
     * @JMS\VirtualProperty(name="active")
     * @OA\Property(description="Indication if the trainTableYear is currently active")
     */
    public function isActive(): bool
    {
        return $this->start_date <= new \DateTime() && $this->end_date >= new \DateTime();
    }
}
