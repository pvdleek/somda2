<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_blokken")
 * @ORM\Entity(repositoryClass="App\Repository\Block")
 */
class Block
{
    /**
     * @ORM\Column(name="blokid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="name", type="string", length=55, nullable=false, options={"default"=""})
     */
    public string $name = '';

    /**
     * @ORM\Column(name="route", type="string", length=45, nullable=false, options={"default"=""})
     */
    public string $route = '';

    /**
     * @ORM\Column(name="role", type="string", length=50, nullable=true)
     */
    public ?string $role = null;

    /**
     * @ORM\Column(name="menu_volgorde", type="integer", nullable=false, options={"default"="1"})
     */
    public int $menuOrder = 1;

    /**
     * @ORM\Column(name="do_separator", type="boolean", nullable=false)
     */
    public bool $doSeparator = false;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\BlockHelp", mappedBy="block")
     */
    public ?BlockHelp $blockHelp = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Block", inversedBy="children")
     * @ORM\JoinColumn(name="parent_block", referencedColumnName="blokid")
     */
    public ?Block $parent = null;

    /**
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
