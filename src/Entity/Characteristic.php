<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

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
     * @var int|null
     * @ORM\Column(name="karakteristiek_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="naam", type="string", length=5, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Name of the characteristic", maxLength=5, type="string")
     */
    public string $name = '';

    /**
     * @var string
     * @ORM\Column(name="omschrijving", type="string", length=25, nullable=false)
     * @SWG\Property(description="Description of the characteristic", maxLength=25, type="string")
     */
    public string $description;
}
