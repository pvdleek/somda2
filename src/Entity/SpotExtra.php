<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_spots_extra')]
class SpotExtra
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Spot::class, inversedBy: 'extra')]
    #[ORM\JoinColumn(name: 'spotid', referencedColumnName: 'spotid')]
    public ?Spot $spot = null;

    #[ORM\Column(length: 255, nullable: false, options: ['default' => ''])]
    public string $extra = '';

    #[ORM\Column(length: 255, nullable: false, options: ['default' => ''])]
    public string $user_extra = '';
}
