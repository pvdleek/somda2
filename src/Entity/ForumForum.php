<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ForumForumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ForumForumRepository::class)]
#[ORM\Table(name: 'somda_forum_forums', indexes: [new ORM\Index(name: 'idx_somda_forum_forums__catid', columns: ['catid'])])]
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

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'forumid', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ForumCategory::class, inversedBy: 'forums')]
    #[ORM\JoinColumn(name: 'catid', referencedColumnName: 'catid')]
    public ?ForumCategory $category = null;

    #[ORM\Column(length: 40, nullable: false, options: ['default' => ''])]
    public string $name = '';

    #[ORM\Column(length: 100, nullable: false, options: ['default' => ''])]
    public string $description = '';

    #[ORM\Column(name: 'volgorde', type: 'smallint', nullable: false, options: ['default' => 1, 'unsigned' => true])]
    public int $order = 1;

    #[ORM\Column(type: 'smallint', nullable: false, options: ['default' => ForumForum::TYPE_LOGGED_IN, 'unsigned' => true])]
    #[Assert\Choice(choices: self::TYPE_VALUES)]
    public int $type = self::TYPE_LOGGED_IN;

    #[ORM\OneToMany(targetEntity: ForumDiscussion::class, mappedBy: 'forum')]
    private Collection $discussions;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'moderated_forums')]
    #[ORM\JoinTable(
        name: 'somda_forum_mods',
        joinColumns: [new ORM\JoinColumn(name: 'forumid', referencedColumnName: 'forumid')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'uid', referencedColumnName: 'uid')]
    )]
    private Collection $moderators;

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
