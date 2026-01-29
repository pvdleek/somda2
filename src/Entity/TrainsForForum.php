<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_trains_for_forum')]
class TrainsForForum
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25, nullable: false, options: ['default' => ''])]
    public string $train_number = '';

    #[ORM\Column(length: 50, nullable: false, options: ['default' => ''])]
    public string $transporter_name = '';

    #[ORM\Column(length: 5, nullable: false, options: ['default' => ''])]
    public string $characteristic_name = '';

    #[ORM\Column(length: 25, nullable: false, options: ['default' => ''])]
    public string $characteristic_description = '';

    #[ORM\Column(length: 10, nullable: false, options: ['default' => ''])]
    public string $first_location_name = '';

    #[ORM\Column(type: 'smallint', options: ['default' => 0, 'unsigned' => true])]
    public int $first_location_time = 0;

    #[ORM\Column(length: 10, nullable: false, options: ['default' => ''])]
    public string $last_location_name = '';

    #[ORM\Column(type: 'smallint', options: ['default' => 0, 'unsigned' => true])]
    public int $last_location_time = 0;

    #[ORM\Column(type: 'string', length: 75, nullable: true)]
    public ?string $section = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
