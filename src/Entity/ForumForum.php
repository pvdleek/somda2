<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    public const TYPE_VALUES = [
        self::TYPE_PUBLIC,
        self::TYPE_LOGGED_IN,
        self::TYPE_MODERATORS_ONLY,
        self::TYPE_ARCHIVE
    ];

    /**
     * @ORM\Column(name="forumid", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumCategory", inversedBy="forums")
     * @ORM\JoinColumn(name="catid", referencedColumnName="catid")
     */
    public ?ForumCategory $category = null;

    /**
     * @ORM\Column(name="name", type="string", length=40, nullable=false)
     */
    public string $name = '';

    /**
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     */
    public string $description = '';

    /**
     * @ORM\Column(name="volgorde", type="smallint", nullable=false, options={"default"=1, "unsigned"=true})
     */
    public int $order = 1;

    /**
     * @ORM\Column(name="type", type="smallint", nullable=false, options={"default"=ForumForum::TYPE_LOGGED_IN, "unsigned"=true})
     * @Assert\Choice(choices=ForumForum::TYPE_VALUES)
     */
    public int $type = self::TYPE_LOGGED_IN;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ForumDiscussion", mappedBy="forum")
     */
    private $discussions;

    /**
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
