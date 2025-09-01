<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ForumPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ForumPostRepository::class)]
#[ORM\Table(name: 'somda_forum_posts', indexes: [
    new ORM\Index(name: 'idx_somda_forum_posts__timestamp', columns: ['timestamp']),
    new ORM\Index(name: 'idx_somda_forum_posts__authorid', columns: ['authorid']),
    new ORM\Index(name: 'idx_somda_forum_posts__discussionid', columns: ['discussionid']),
])]
class ForumPost
{
    public const WIKI_CHECK_NOT_CHECKED = 0;
    public const WIKI_CHECK_OK = 1;
    public const WIKI_CHECK_N_A = 2;
    public const WIKI_CHECK_VALUES = [self::WIKI_CHECK_NOT_CHECKED, self::WIKI_CHECK_OK, self::WIKI_CHECK_N_A];

    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'postid', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The author of the post", ref=@Model(type=User::class))
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'authorid', referencedColumnName: 'uid')]
    public ?User $author = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\ManyToOne(targetEntity: ForumDiscussion::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(name: 'discussionid', referencedColumnName: 'discussionid')]
    public ?ForumDiscussion $discussion = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the post (Y-m-dTH:i:sP)", type="string")
     */
    #[ORM\Column(nullable: true)]
    public ?\DateTime $timestamp = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The text of the post", ref=@Model(type=ForumPostText::class))
     */
    #[ORM\OneToOne(targetEntity: ForumPostText::class, mappedBy: 'post', cascade: ['persist', 'remove'])]
    public ?ForumPostText $text = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the post edit (Y-m-dTH:i:sP)", type="string")
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTime $edit_timestamp = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The user that edited the post", ref=@Model(type=User::class))
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'edit_uid', referencedColumnName: 'uid')]
    public ?User $editor = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Reason for editing the post", maxLength=50, type="string")
     */
    #[ORM\Column(length: 50, nullable: true)]
    public ?string $edit_reason = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Whether the signature of the author is included", type="boolean")
     */
    #[ORM\Column(name: 'sign_on', nullable: false, options: ['default' => false])]
    public bool $signature_on = false;

    /**
     * @JMS\Exclude()
     */
    #[ORM\Column(type: 'smallint', nullable: false, options: ['default' => self::WIKI_CHECK_NOT_CHECKED, 'unsigned' => true])]
    #[Assert\Choice(choices: self::WIKI_CHECK_VALUES)]
    public int $wiki_check = self::WIKI_CHECK_NOT_CHECKED;

    /**
     * @JMS\Exclude()
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'wiki_uid', referencedColumnName: 'uid')]
    public ?User $wiki_checker = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\OneToMany(targetEntity: ForumPostAlert::class, mappedBy: 'post')]
    private Collection $alerts;

    /**
     * @JMS\Exclude()
     */
    #[ORM\OneToMany(targetEntity: ForumPostLog::class, mappedBy: 'post')]
    private Collection $logs;

    /**
     * @JMS\Exclude()
     */
    #[ORM\OneToMany(targetEntity: ForumSearchList::class, mappedBy: 'post')]
    private Collection $search_lists;

    #[ORM\OneToMany(targetEntity: ForumPostFavorite::class, mappedBy: 'post')]
    private Collection $favorites;

    public function __construct()
    {
        $this->alerts = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->search_lists = new ArrayCollection();
        $this->favorites = new ArrayCollection();
    }

    public function addAlert(ForumPostAlert $forumPostAlert): ForumPost
    {
        $this->alerts[] = $forumPostAlert;
        $forumPostAlert->post = $this;
        return $this;
    }

    /**
     * @return ForumPostAlert[]
     */
    public function getAlerts(): array
    {
        return $this->alerts->toArray();
    }

    public function addLog(ForumPostLog $forumPostLog): ForumPost
    {
        $this->logs[] = $forumPostLog;
        $forumPostLog->post = $this;
        return $this;
    }

    /**
     * @return ForumPostLog[]
     */
    public function getLogs(): array
    {
        return $this->logs->toArray();
    }

    public function addSearchList(ForumSearchList $forumSearchList): ForumPost
    {
        $this->search_lists[] = $forumSearchList;
        $forumSearchList->post = $this;
        return $this;
    }

    /**
     * @return ForumSearchList[]
     */
    public function getSearchLists(): array
    {
        return $this->search_lists->toArray();
    }

    public function addFavorite(ForumPostFavorite $forumPostFavorite): ForumPost
    {
        $this->favorites[] = $forumPostFavorite;
        return $this;
    }

    /**
     * @return ForumPostFavorite[]
     */
    public function getFavorites(): array
    {
        return $this->favorites->toArray();
    }

    public function getNumberOfFavorites(): int
    {
        return $this->favorites->count();
    }
}
