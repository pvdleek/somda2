<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(name="somda_positie")
 * @ORM\Entity(repositoryClass="App\Repository\Position")
 */
class Position
{
    /**
     * @ORM\Column(name="posid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="positie", type="string", length=2, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="The position", maxLength=2, type="string")
     */
    public string $name = '';
}
