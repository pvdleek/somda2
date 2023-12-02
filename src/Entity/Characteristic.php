<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(
 *     name="somda_karakteristiek",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_48102_omschrijving", columns={"naam"})}
 * )
 * @ORM\Entity
 */
class Characteristic
{
    /**
     * @ORM\Column(name="karakteristiek_id", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="naam", type="string", length=5, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Name of the characteristic", maxLength=5, type="string")
     */
    public string $name = '';

    /**
     * @ORM\Column(name="omschrijving", type="string", length=25, nullable=false)
     * @OA\Property(description="Description of the characteristic", maxLength=25, type="string")
     */
    public string $description = '';
}
