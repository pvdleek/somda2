<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_help')]
class Help
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'contentid', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'titel', type: 'text', nullable: false, options: ['default' => ''])]
    public string $title = '';

    #[ORM\Column(nullable: false, options: ['default' => ''])]
    public string $template = '';
}
