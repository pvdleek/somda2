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
    const BLOCK_TYPE_SECURED = 1;
    const BLOCK_TYPE_PUBLIC = 2;
    const BLOCK_TYPE_LOGGED_IN = 3;
    const BLOCK_TYPE_LOGGED_OUT = 4;

    /**
     * @var int
     * @ORM\Column(name="blokid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=55, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="url", type="string", length=45, nullable=false)
     */
    private $url;

    /**
     * @var string
     * @ORM\Column(name="url_short", type="string", length=20, nullable=false)
     */
    private $urlShort;

    /**
     * @var string
     * @ORM\Column(name="role", type="string", length=50, nullable=true)
     */
    private $role;

    /**
     * @var int
     * @ORM\Column(name="menu_volgorde", type="bigint", nullable=false)
     */
    private $menuOrder;

    /**
     * @var int
     * @ORM\Column(name="do_separator", type="boolean", nullable=false)
     */
    private $doSeparator = false;

    /**
     * @var int|null
     * @ORM\Column(name="sitemap_last_update", type="bigint", nullable=true)
     */
    private $siteMapLastUpdate;

    /**
     * @var string|null
     * @ORM\Column(name="sitemap_frequency", type="string", length=10, nullable=true)
     */
    private $siteMapFrequency;

    /**
     * @var int|null
     * @ORM\Column(name="sitemap_prio", type="bigint", nullable=true)
     */
    private $siteMapPriority;

    /**
     * @var Block
     * @ORM\ManyToOne(targetEntity="App\Entity\Block", inversedBy="children")
     * @ORM\JoinColumn(name="parent_block", referencedColumnName="blokid")
     */
    private $parent;

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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Block
     */
    public function setId(int $id): Block
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Block
     */
    public function setName(string $name): Block
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Block
     */
    public function setUrl(string $url): Block
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrlShort(): string
    {
        return $this->urlShort;
    }

    /**
     * @param string $urlShort
     * @return Block
     */
    public function setUrlShort(string $urlShort): Block
    {
        $this->urlShort = $urlShort;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @param string $role
     * @return Block
     */
    public function setRole(string $role): Block
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return int
     */
    public function getMenuOrder(): int
    {
        return $this->menuOrder;
    }

    /**
     * @param int $menuOrder
     * @return Block
     */
    public function setMenuOrder(int $menuOrder): Block
    {
        $this->menuOrder = $menuOrder;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoSeparator(): int
    {
        return $this->doSeparator;
    }

    /**
     * @param int $doSeparator
     * @return Block
     */
    public function setDoSeparator(int $doSeparator): Block
    {
        $this->doSeparator = $doSeparator;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSiteMapLastUpdate(): ?int
    {
        return $this->siteMapLastUpdate;
    }

    /**
     * @param int|null $siteMapLastUpdate
     * @return Block
     */
    public function setSiteMapLastUpdate(?int $siteMapLastUpdate): Block
    {
        $this->siteMapLastUpdate = $siteMapLastUpdate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSiteMapFrequency(): ?string
    {
        return $this->siteMapFrequency;
    }

    /**
     * @param string|null $siteMapFrequency
     * @return Block
     */
    public function setSiteMapFrequency(?string $siteMapFrequency): Block
    {
        $this->siteMapFrequency = $siteMapFrequency;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSiteMapPriority(): ?int
    {
        return $this->siteMapPriority;
    }

    /**
     * @param int|null $siteMapPriority
     * @return Block
     */
    public function setSiteMapPriority(?int $siteMapPriority): Block
    {
        $this->siteMapPriority = $siteMapPriority;
        return $this;
    }

    /**
     * @return Block
     */
    public function getParent(): Block
    {
        return $this->parent;
    }

    /**
     * @param Block $parent
     * @return Block
     */
    public function setParent(Block $parent): Block
    {
        $this->parent = $parent;
        return $this;
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
