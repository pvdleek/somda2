<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_forums", indexes={@ORM\Index(name="idx_47937_catid", columns={"catid"})})
 * @ORM\Entity(repositoryClass="App\Repository\ForumForum")
 */
class ForumForum
{
    /**
     * @var int
     * @ORM\Column(name="forumid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var ForumCategory
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumCategory", inversedBy="forums")
     * @ORM\JoinColumn(name="catid", referencedColumnName="catid")
     */
    private $category;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=40, nullable=false)
     */
    private $name = '';

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     */
    private $description = '';

    /**
     * @var int
     * @ORM\Column(name="volgorde", type="bigint", nullable=false)
     */
    private $order;

    /**
     * @var int
     * @ORM\Column(name="type", type="bigint", nullable=false)
     */
    private $type = 1;

    /**
     * @var ForumDiscussion[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumDiscussion", mappedBy="forum")
     */
    private $discussions;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ForumForum
     */
    public function setId(int $id): ForumForum
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return ForumCategory
     */
    public function getCategory(): ForumCategory
    {
        return $this->category;
    }

    /**
     * @param ForumCategory $category
     * @return ForumForum
     */
    public function setCategory(ForumCategory $category): ForumForum
    {
        $this->category = $category;
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
     * @return ForumForum
     */
    public function setName(string $name): ForumForum
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return ForumForum
     */
    public function setDescription(string $description): ForumForum
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return ForumForum
     */
    public function setOrder(int $order): ForumForum
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return ForumForum
     */
    public function setType(int $type): ForumForum
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param ForumDiscussion $forumDiscussion
     * @return ForumForum
     */
    public function addDiscussion(ForumDiscussion $forumDiscussion): ForumForum
    {
        $this->discussions[] = $forumDiscussion;
        return $this;
    }

    /**
     * @return ForumDiscussion[]
     */
    public function getDiscussions(): array
    {
        return $this->discussions->toArray();
    }
}
