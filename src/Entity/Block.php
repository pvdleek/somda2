<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BlockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlockRepository::class)]
#[ORM\Table(name: 'somda_blokken')]
class Block
{
    #[ORM\Column(name: 'blokid', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    public ?int $id = null;

    #[ORM\Column(length: 55, nullable: false, options: ['default' => ''])]
    public string $name = '';

    #[ORM\Column(length: 45, nullable: false, options: ['default' => ''])]
    public string $route = '';

    #[ORM\Column(length: 50, nullable: true)]
    public ?string $role = null;

    #[ORM\Column(name: 'menu_volgorde', type: 'smallint', nullable: false, options: ['default' => 1, 'unsigned' => true])]
    public int $menu_order = 1;

    #[ORM\OneToOne(targetEntity: BlockHelp::class, mappedBy: 'block')]
    public ?BlockHelp $block_help = null;

    #[ORM\ManyToOne(targetEntity: Block::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_block', referencedColumnName: 'blokid')]
    public ?Block $parent = null;

    #[ORM\OneToMany(targetEntity: Block::class, mappedBy: 'parent')]
    private Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function addChild(Block $child): Block
    {
        $this->children[] = $child;
        return $this;
    }

    /**
     * @return Block[]
     */
    public function getChildren(): array
    {
        return $this->children->toArray();
    }
}
