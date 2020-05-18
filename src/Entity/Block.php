<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_blokken")
 * @ORM\Entity(repositoryClass="App\Repository\Block")
 */
class Block extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="blokid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=55, nullable=false, options={"default"=""})
     */
    public string $name = '';

    /**
     * @var string
     * @ORM\Column(name="route", type="string", length=45, nullable=false, options={"default"=""})
     */
    public string $route = '';

    /**
     * @var string|null
     * @ORM\Column(name="role", type="string", length=50, nullable=true)
     */
    public ?string $role;

    /**
     * @var int
     * @ORM\Column(name="menu_volgorde", type="integer", nullable=false, options={"default"="1"})
     */
    public int $menuOrder = 1;

    /**
     * @var bool
     * @ORM\Column(name="do_separator", type="boolean", nullable=false)
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
     * @ORM\JoinColumn(name="parent_block", referencedColumnName="blokid")
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
