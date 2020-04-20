<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_posts", indexes={@ORM\Index(name="idx_47961_timestamp", columns={"timestamp"}), @ORM\Index(name="idx_47961_authorid", columns={"authorid"}), @ORM\Index(name="idx_47961_discussionid", columns={"discussionid"})})
 * @ORM\Entity
 */
class ForumPost extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="postid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="authorid", referencedColumnName="uid")
     */
    public $author;

    /**
     * @var ForumDiscussion
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumDiscussion", inversedBy="posts")
     * @ORM\JoinColumn(name="discussionid", referencedColumnName="discussionid")
     */
    public $discussion;

    /**
     * @var DateTime
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    public $timestamp;

    /**
     * @var ForumPostText
     * @ORM\OneToOne(targetEntity="App\Entity\ForumPostText", mappedBy="post")
     */
    public $text;

    /**
     * @var DateTime|null
     * @ORM\Column(name="edit_timestamp", type="datetime", nullable=true)
     */
    public $editTimestamp;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="edit_uid", referencedColumnName="uid")
     */
    public $editor;

    /**
     * @var string|null
     * @ORM\Column(name="edit_reason", type="string", length=50, nullable=true)
     */
    public $editReason;

    /**
     * @var boolean
     * @ORM\Column(name="sign_on", type="boolean", nullable=false)
     */
    public $signatureOn = false;

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
    public $wikiChecker;

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
}
