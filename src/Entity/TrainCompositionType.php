<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_mat_types')]
class TrainCompositionType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'typeid', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'omschrijving', length: 25, nullable: false, options: ['default' => ''])]
    public string $description = '';

    #[ORM\Column(name: 'bak1', length: 15, nullable: true)]
    public ?string $car1 = null;

    #[ORM\Column(name: 'bak2', length: 15, nullable: true)]
    public ?string $car2 = null;

    #[ORM\Column(name: 'bak3', length: 15, nullable: true)]
    public ?string $car3 = null;

    #[ORM\Column(name: 'bak4', length: 15, nullable: true)]
    public ?string $car4 = null;

    #[ORM\Column(name: 'bak5', length: 15, nullable: true)]
    public ?string $car5 = null;

    #[ORM\Column(name: 'bak6', length: 15, nullable: true)]
    public ?string $car6 = null;

    #[ORM\Column(name: 'bak7', length: 15, nullable: true)]
    public ?string $car7 = null;

    #[ORM\Column(name: 'bak8', length: 15, nullable: true)]
    public ?string $car8 = null;

    #[ORM\Column(name: 'bak9', length: 15, nullable: true)]
    public ?string $car9 = null;

    #[ORM\Column(name: 'bak10', length: 15, nullable: true)]
    public ?string $car10 = null;

    #[ORM\Column(name: 'bak11', length: 15, nullable: true)]
    public ?string $car11 = null;

    #[ORM\Column(name: 'bak12', length: 15, nullable: true)]
    public ?string $car12 = null;

    #[ORM\Column(name: 'bak13', length: 15, nullable: true)]
    public ?string $car13 = null;

    public function getCar(int $car_number): ?string
    {
        return $this->{'car'.$car_number};
    }
}
