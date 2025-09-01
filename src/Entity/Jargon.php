<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_jargon')]
class Jargon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'jargonid', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(length: 15, nullable: false, options: ['default' => ''])]
    public string $term = '';

    #[ORM\Column(length: 20, nullable: false, options: ['default' => ''])]
    public string $image = '';

    #[ORM\Column(length: 150, nullable: false, options: ['default' => ''])]
    public string $description = '';
}
