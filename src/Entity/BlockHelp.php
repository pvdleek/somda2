<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_help_text')]
class BlockHelp
{
    #[ORM\OneToOne(targetEntity: Block::class, inversedBy: 'block_help')]
    #[ORM\JoinColumn(name: 'blokid', referencedColumnName: 'blokid')]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    public ?Block $block = null;

    #[ORM\Column(type: 'text', nullable: false, options: ['default' => ''])]
    public string $text = '';
}
