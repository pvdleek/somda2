<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(name="spe_spot_extra")
 * @ORM\Entity
 */
class SpotExtra
{
    /**
     * @var Spot
     * @ORM\OneToOne(targetEntity="App\Entity\Spot", inversedBy="extra")
     * @ORM\JoinColumn(name="spe_spo_id", referencedColumnName="spo_id")
     * @ORM\Id
     * @JMS\Exclude()
     */
    public Spot $spot;

    /**
     * @var string
     * @ORM\Column(name="spe_extra", type="string", length=255, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Extra information", maxLength=255, type="string")
     */
    public string $extra = '';

    /**
     * @var string
     * @ORM\Column(name="spe_user_extra", type="string", length=255, nullable=false)
     * @JMS\Exclude()
     */
    public string $userExtra = '';
}
