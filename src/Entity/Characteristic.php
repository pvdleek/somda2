<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

#[ORM\Entity]
#[ORM\Table(name: 'somda_karakteristiek', uniqueConstraints: [new ORM\UniqueConstraint(name: 'unq_somda_karakteristiek__naam', columns: ['naam'])])]
class Characteristic
{
    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'karakteristiek_id', type: 'smallint', options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Name of the characteristic", maxLength=5, type="string")
     */
    #[ORM\Column(name: 'naam', length: 5, nullable: false, options: ['default' => ''])]
    public string $name = '';

    /**
     * @OA\Property(description="Description of the characteristic", maxLength=25, type="string")
     */
    #[ORM\Column(name: 'omschrijving', length: 25, nullable: false, options: ['default' => ''])]
    public string $description = '';
}
