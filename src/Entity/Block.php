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
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=55, nullable=false)
     */
    public $name;

    /**
     * @var string
     * @ORM\Column(name="route", type="string", length=45, nullable=false)
     */
    public $route;

    /**
     * @var string
     * @ORM\Column(name="role", type="string", length=50, nullable=true)
     */
    public $role;

    /**
     * @var int
     * @ORM\Column(name="menu_volgorde", type="bigint", nullable=false)
     */
    public $menuOrder;

    /**
     * @var int
     * @ORM\Column(name="do_separator", type="boolean", nullable=false)
     */
    public $doSeparator = false;

    /**
     * @var Block
     * @ORM\ManyToOne(targetEntity="App\Entity\Block", inversedBy="children")
     * @ORM\JoinColumn(name="parent_block", referencedColumnName="blokid")
     */
    public $parent;

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
