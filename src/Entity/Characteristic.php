<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(
 *     name="cha_characteristic",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="UNQ_cha_name", columns={"cha_name"})}
 * )
 * @ORM\Entity
 */
class Characteristic
{
    /**
     * @var int|null
     * @ORM\Column(name="cha_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="cha_name", type="string", length=5, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Name of the characteristic", maxLength=5, type="string")
     */
    public string $name = '';

    /**
     * @var string
     * @ORM\Column(name="cha_description", type="string", length=25, nullable=false)
     * @SWG\Property(description="Description of the characteristic", maxLength=25, type="string")
     */
    public string $description;
}
