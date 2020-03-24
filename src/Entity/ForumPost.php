<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_posts", indexes={@ORM\Index(name="idx_47961_timestamp", columns={"timestamp"}), @ORM\Index(name="idx_47961_authorid", columns={"authorid"}), @ORM\Index(name="idx_47961_discussionid", columns={"discussionid"})})
 * @ORM\Entity
 */
class ForumPost
{
    /**
     * @var int
     * @ORM\Column(name="postid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="authorid", referencedColumnName="uid")
     */
    private $author;

    /**
     * @var ForumDiscussion
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumDiscussion", inversedBy="posts")
     * @ORM\JoinColumn(name="discussionid", referencedColumnName="discussionid")
     */
    private $discussion;

    /**
     * @var DateTime
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var ForumPostText
     * @ORM\OneToOne(targetEntity="App\Entity\ForumPostText", mappedBy="post")
     */
    private $text;

    /**
     * @var DateTime|null
     * @ORM\Column(name="edit_timestamp", type="datetime", nullable=true)
     */
    private $editTimestamp;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="edit_uid", referencedColumnName="uid")
     */
    private $editor;

    /**
     * @var string|null
     * @ORM\Column(name="edit_reason", type="string", length=50, nullable=true)
     */
    private $editReason;

    /**
     * @var boolean
     * @ORM\Column(name="sign_on", type="boolean", nullable=false)
     */
    private $signatureOn = false;

    /**
     * @var boolean
     * @ORM\Column(name="wiki_check", type="boolean", nullable=false)
     */
    private $wikiCheck = false;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="wiki_uid", referencedColumnName="uid")
     */
    private $wikiChecker;

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
     *
     */
    public function __construct()
    {
        $this->alerts = new ArrayCollection();
        $this->logs = new ArrayCollection();
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
     * @return ForumPost
     */
    public function setId(int $id): ForumPost
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return ForumPost
     */
    public function setAuthor(User $author): ForumPost
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return ForumDiscussion
     */
    public function getDiscussion(): ForumDiscussion
    {
        return $this->discussion;
    }

    /**
     * @param ForumDiscussion $discussion
     * @return ForumPost
     */
    public function setDiscussion(ForumDiscussion $discussion): ForumPost
    {
        $this->discussion = $discussion;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param DateTime $timestamp
     * @return ForumPost
     */
    public function setTimestamp(DateTime $timestamp): ForumPost
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return ForumPostText
     */
    public function getText(): ForumPostText
    {
        return $this->text;
    }

    /**
     * @param ForumPostText $text
     * @return ForumPost
     */
    public function setText(ForumPostText $text): ForumPost
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEditTimestamp(): ?DateTime
    {
        return $this->editTimestamp;
    }

    /**
     * @param DateTime|null $editTimestamp
     * @return ForumPost
     */
    public function setEditTimestamp(?DateTime $editTimestamp): ForumPost
    {
        $this->editTimestamp = $editTimestamp;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getEditor(): ?User
    {
        return $this->editor;
    }

    /**
     * @param User|null $editor
     * @return ForumPost
     */
    public function setEditor(?User $editor): ForumPost
    {
        $this->editor = $editor;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEditReason(): ?string
    {
        return $this->editReason;
    }

    /**
     * @param string|null $editReason
     * @return ForumPost
     */
    public function setEditReason(?string $editReason): ForumPost
    {
        $this->editReason = $editReason;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSignatureOn(): bool
    {
        return $this->signatureOn;
    }

    /**
     * @param bool $signatureOn
     * @return ForumPost
     */
    public function setSignatureOn(bool $signatureOn): ForumPost
    {
        $this->signatureOn = $signatureOn;
        return $this;
    }

    /**
     * @return bool
     */
    public function isWikiCheck(): bool
    {
        return $this->wikiCheck;
    }

    /**
     * @param bool $wikiCheck
     * @return ForumPost
     */
    public function setWikiCheck(bool $wikiCheck): ForumPost
    {
        $this->wikiCheck = $wikiCheck;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getWikiChecker(): ?User
    {
        return $this->wikiChecker;
    }

    /**
     * @param User|null $wikiChecker
     * @return ForumPost
     */
    public function setWikiChecker(?User $wikiChecker): ForumPost
    {
        $this->wikiChecker = $wikiChecker;
        return $this;
    }

    /**
     * @param ForumPostAlert $forumPostAlert
     * @return ForumPost
     */
    public function addAlert(ForumPostAlert $forumPostAlert): ForumPost
    {
        $this->alerts[] = $forumPostAlert;
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
        return $this;
    }

    /**
     * @return ForumPostLog[]
     */
    public function getLogs(): array
    {
        return $this->logs->toArray();
    }
}
