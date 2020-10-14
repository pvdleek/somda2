<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="fof_forum_forum", indexes={@ORM\Index(name="idx_47937_catid", columns={"fof_foc_id"})})
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
     * @var int|null
     * @ORM\Column(name="fof_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var ForumCategory
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumCategory", inversedBy="forums")
     * @ORM\JoinColumn(name="fof_foc_id", referencedColumnName="foc_id")
     */
    public ForumCategory $category;

    /**
     * @var string
     * @ORM\Column(name="fof_name", type="string", length=40, nullable=false)
     */
    public string $name = '';

    /**
     * @var string
     * @ORM\Column(name="fof_description", type="string", length=100, nullable=false)
     */
    public string $description = '';

    /**
     * @var int
     * @ORM\Column(name="fof_order", type="integer", nullable=false, options={"default"="1"})
     */
    public int $order = 1;

    /**
     * @var int
     * @ORM\Column(name="fof_type", type="integer", nullable=false, options={"default"=ForumForum::TYPE_LOGGED_IN})
     * @Assert\Choice(choices=ForumForum::TYPE_VALUES)
     */
    public int $type = self::TYPE_LOGGED_IN;

    /**
     * @var ForumDiscussion[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumDiscussion", mappedBy="forum")
     */
    private $discussions;

    /**
     * @var User[]
     * @ORM\ManyToMany(targetEntity="User", inversedBy="moderatedForums")
     * @ORM\JoinTable(name="ffm_forum_forum_moderator",
     *      joinColumns={@ORM\JoinColumn(name="ffm_fof_id", referencedColumnName="fof_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="ffm_use_id", referencedColumnName="use_id")}
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
