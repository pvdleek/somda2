<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_spot_punt_text')]
class PoiText
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Poi::class, inversedBy: 'text')]
    #[ORM\JoinColumn(name: 'puntid', referencedColumnName: 'puntid')]
    public ?Poi $poi = null;

    #[ORM\Column(name: 'route_auto', type: 'text', nullable: false, options: ['default' => ''])]
    public string $route_car = '';

    #[ORM\Column(name: 'route_ov', type: 'text', nullable: false, options: ['default' => ''])]
    public string $route_public_transport = '';

    #[ORM\Column(name: 'bijzonderheden', type: 'text', nullable: false, options: ['default' => ''])]
    public string $particularities = '';
}
