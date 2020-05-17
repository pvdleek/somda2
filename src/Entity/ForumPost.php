<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *     name="somda_forum_posts",
 *     indexes={
 *         @ORM\Index(name="idx_47961_timestamp", columns={"timestamp"}),
 *         @ORM\Index(name="idx_47961_authorid", columns={"authorid"}),
 *         @ORM\Index(name="idx_47961_discussionid", columns={"discussionid"})
 *     }
 * )
 * @ORM\Entity
 */
class ForumPost extends Entity
{
    public const WIKI_CHECK_NOT_CHECKED = 0;
    public const WIKI_CHECK_OK = 1;
    public const WIKI_CHECK_N_A = 2;
    public const WIKI_CHECK_VALUES = [self::WIKI_CHECK_NOT_CHECKED, self::WIKI_CHECK_OK, self::WIKI_CHECK_N_A];

    /**
     * @var int
     * @ORM\Column(name="postid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="authorid", referencedColumnName="uid")
     */
    public User $author;

    /**
     * @var ForumDiscussion
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumDiscussion", inversedBy="posts")
     * @ORM\JoinColumn(name="discussionid", referencedColumnName="discussionid")
     */
    public ForumDiscussion $discussion;

    /**
     * @var DateTime
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var ForumPostText
     * @ORM\OneToOne(targetEntity="App\Entity\ForumPostText", mappedBy="post")
     */
    public ForumPostText $text;

    /**
     * @var DateTime|null
     * @ORM\Column(name="edit_timestamp", type="datetime", nullable=true)
     */
    public ?DateTime $editTimestamp;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="edit_uid", referencedColumnName="uid")
     */
    public ?User $editor;

    /**
     * @var string|null
     * @ORM\Column(name="edit_reason", type="string", length=50, nullable=true)
     */
    public ?string $editReason;

    /**
     * @var bool
     * @ORM\Column(name="sign_on", type="boolean", nullable=false)
     */
    public bool $signatureOn = false;

    /**
     * @var integer
     * @ORM\Column(name="wiki_check", type="integer", nullable=false)
     * @Assert\Choice(choices=ForumPost::WIKI_CHECK_VALUES)
     */
    public int $wikiCheck = self::WIKI_CHECK_NOT_CHECKED;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="wiki_uid", referencedColumnName="uid")
     */
    public ?User $wikiChecker;

    /**
     * @var ForumPostAlert[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumPostAlert", mappedBy="post")
     */
    private $alerts;

    /**
     * @var ForumPostLog[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumPostLog", mappedBy="post")
     */
    private $logs;

    /**
     * @var ForumSearchList[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumSearchList", mappedBy="post")
     */
    private $searchLists;

    /**
     *
     */
    public function __construct()
    {
        $this->alerts = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->searchLists = new ArrayCollection();
    }

    /**
     * @param ForumPostAlert $forumPostAlert
     * @return ForumPost
     */
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

    /**
     * @param ForumPostLog $forumPostLog
     * @return ForumPost
     */
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

    /**
     * @param ForumSearchList $forumSearchList
     * @return ForumPost
     */
    public function addSearchList(ForumSearchList $forumSearchList): ForumPost
    {
        $this->searchLists[] = $forumSearchList;
        $forumSearchList->post = $this;
        return $this;
    }

    /**
     * @return ForumSearchList[]
     */
    public function getSearchLists(): array
    {
        return $this->searchLists->toArray();
    }
}
