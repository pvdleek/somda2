<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PositionRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

#[ORM\Entity(repositoryClass: PositionRepository::class)]
#[ORM\Table(name: 'somda_positie')]
class Position
{
    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'posid', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The position", maxLength=2, type="string")
     */
    #[ORM\Column(name: 'positie', length: 2, nullable: false, options: ['default' => ''])]
    public string $name = '';
}
