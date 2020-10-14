<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="blo_block")
 * @ORM\Entity(repositoryClass="App\Repository\Block")
 */
class Block
{
    /**
     * @var int|null
     * @ORM\Column(name="blo_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="blo_name", type="string", length=55, nullable=false, options={"default"=""})
     */
    public string $name = '';

    /**
     * @var string
     * @ORM\Column(name="blo_route", type="string", length=45, nullable=false, options={"default"=""})
     */
    public string $route = '';

    /**
     * @var string|null
     * @ORM\Column(name="blo_role", type="string", length=50, nullable=true)
     */
    public ?string $role;

    /**
     * @var int
     * @ORM\Column(name="blo_menu_order", type="integer", nullable=false, options={"default"="1"})
     */
    public int $menuOrder = 1;

    /**
     * @var bool
     * @ORM\Column(name="blo_do_separator", type="boolean", nullable=false)
     */
    public bool $doSeparator = false;

    /**
     * @var BlockHelp|null
     * @ORM\OneToOne(targetEntity="App\Entity\BlockHelp", mappedBy="block")
     */
    public ?BlockHelp $blockHelp;

    /**
     * @var Block
     * @ORM\ManyToOne(targetEntity="App\Entity\Block", inversedBy="children")
     * @ORM\JoinColumn(name="blo_parent_blo_id", referencedColumnName="blo_id")
     */
    public Block $parent;

    /**
     * @var Block[]
     * @ORM\OneToMany(targetEntity="App\Entity\Block", mappedBy="parent")
     */
    private $children;

    /**
     *
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @param Block $child
     * @return Block
     */
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
