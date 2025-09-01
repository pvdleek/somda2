<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

#[ORM\Entity]
#[ORM\Table(name: 'somda_spots_extra')]
class SpotExtra
{
    /**
     * @JMS\Exclude()
     */
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Spot::class, inversedBy: 'extra')]
    #[ORM\JoinColumn(name: 'spotid', referencedColumnName: 'spotid')]
    public ?Spot $spot = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Extra information", maxLength=255, type="string")
     */
    #[ORM\Column(length: 255, nullable: false, options: ['default' => ''])]
    public string $extra = '';

    /**
     * @JMS\Exclude()
     */
    #[ORM\Column(length: 255, nullable: false, options: ['default' => ''])]
    public string $user_extra = '';
}
