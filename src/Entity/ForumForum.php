<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_forums", indexes={@ORM\Index(name="idx_47937_catid", columns={"catid"})})
 * @ORM\Entity(repositoryClass="App\Repository\ForumForum")
 */
class ForumForum
{
    public const TYPE_PUBLIC = 0;
    public const TYPE_LOGGED_IN = 1;
    public const TYPE_MODERATORS_ONLY = 3;
    public const TYPE_ARCHIVE = 4;

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
    private $type = self::TYPE_LOGGED_IN;

    /**
     * @var ForumDiscussion[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumDiscussion", mappedBy="forum")
     */
    private $discussions;

    /**
     * @var User[]
     * @ORM\ManyToMany(targetEntity="User", inversedBy="moderatedForums")
     * @ORM\JoinTable(name="somda_forum_mods",
     *      joinColumns={@ORM\JoinColumn(name="forumid", referencedColumnName="forumid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="uid", referencedColumnName="uid")}
     * )
     */
    private $moderators;

    /**
     *
     */
    public function __construct()
    {
        $this->discussions = new ArrayCollection();
        $this->moderators = new ArrayCollection();
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

    /**
     * @param User $user
     * @return ForumForum
     */
    public function addModerator(User $user): ForumForum
    {
        $this->moderators[] = $user;
        return $this;
    }

    /**
     * @return User[]
     */
    public function getModerators(): array
    {
        return $this->moderators->toArray();
    }
}
