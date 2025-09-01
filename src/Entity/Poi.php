<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_spot_punt')]
class Poi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'puntid', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'naam', length: 50, nullable: false, options: ['default' => ''])]
    public string $name = '';

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'pois')]
    #[ORM\JoinColumn(name: 'afkid_locatie', referencedColumnName: 'afkid')]
    public ?Location $location = null;

    #[ORM\ManyToOne(targetEntity: Location::class)]
    #[ORM\JoinColumn(name: 'afkid_traject_1', referencedColumnName: 'afkid')]
    public ?Location $location_section1 = null;

    #[ORM\ManyToOne(targetEntity: Location::class)]
    #[ORM\JoinColumn(name: 'afkid_traject_2', referencedColumnName: 'afkid')]
    public ?Location $location_section2 = null;

    #[ORM\ManyToOne(targetEntity: Location::class)]
    #[ORM\JoinColumn(name: 'afkid_dks', referencedColumnName: 'afkid')]
    public ?Location $location_for_routes = null;

    #[ORM\Column(name: 'kilometrering', length: 25, nullable: true)]
    public ?string $kilometre = null;

    #[ORM\Column(length: 25, nullable: true)]
    public ?string $gps = null;

    #[ORM\Column(name: 'zonstand_winter', length: 50, nullable: true)]
    public ?string $sun_position_winter = null;

    #[ORM\Column(name: 'zonstand_zomer', length: 50, nullable: true)]
    public ?string $sun_position_summer = null;

    #[ORM\Column(name: 'google_url', length: 200, nullable: true)]
    public ?string $google_url = null;

    #[ORM\Column(name: 'foto', length: 25, nullable: false, options: ['default' => 'geen_foto.jpg'])]
    public string $photo = 'geen_foto.jpg';

    #[ORM\ManyToOne(targetEntity: PoiCategory::class, inversedBy: 'pois')]
    #[ORM\JoinColumn(name: 'provincieid', referencedColumnName: 'provincieid')]
    public ?PoiCategory $category = null;

    #[ORM\OneToOne(targetEntity: PoiText::class, mappedBy: 'poi')]
    public ?PoiText $text = null;
}
