<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(name="somda_spots_extra")
 * @ORM\Entity
 */
class SpotExtra
{
    /**
     * @var Spot
     * @ORM\OneToOne(targetEntity="App\Entity\Spot", inversedBy="extra")
     * @ORM\JoinColumn(name="spotid", referencedColumnName="spotid")
     * @ORM\Id
     * @JMS\Exclude()
     */
    public Spot $spot;

    /**
     * @var string
     * @ORM\Column(name="extra", type="string", length=255, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Extra information", maxLength=255, type="string")
     */
    public string $extra = '';

    /**
     * @var string
     * @ORM\Column(name="user_extra", type="string", length=255, nullable=false)
     * @JMS\Exclude()
     */
    public string $userExtra = '';
}
