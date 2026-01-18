<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_mat_patterns')]
#[ORM\UniqueConstraint(name: 'unq_somda_mat_patterns__volgorde', columns: ['volgorde'])]
class TrainNamePattern
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'volgorde', nullable: false, options: ['default' => 1, 'unsigned' => true])]
    public int $order = 1;

    #[ORM\Column(length: 80, nullable: false, options: ['default' => ''])] 
    public string $pattern = '';

    #[ORM\Column(name: 'naam', length: 50, nullable: false, options: ['default' => ''])]
    public string $name = '';

    #[ORM\Column(name: 'tekening', length: 30, nullable: true)]
    public ?string $image = null;
}
