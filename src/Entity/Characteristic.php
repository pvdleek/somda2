<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_karakteristiek')]
#[ORM\UniqueConstraint(name: 'unq_somda_karakteristiek__naam', columns: ['naam'])]
class Characteristic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'karakteristiek_id', type: 'smallint', options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'naam', length: 5, nullable: false, options: ['default' => ''])]
    public string $name = '';

    #[ORM\Column(name: 'omschrijving', length: 25, nullable: false, options: ['default' => ''])]
    public string $description = '';
}
