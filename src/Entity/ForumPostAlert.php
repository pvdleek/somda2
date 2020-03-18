<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_alerts", indexes={@ORM\Index(name="idx_47886_postid", columns={"postid"})})
 * @ORM\Entity
 */
class ForumPostAlert
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var ForumPost
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost", inversedBy="alerts")
     * @ORM\JoinColumn(name="postid", referencedColumnName="postid")
     */
    private $post;

    /**
     * @var boolean
     * @ORM\Column(name="closed", type="boolean", nullable=false)
     */
    private $closed = false;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="senderid", referencedColumnName="uid")
     */
    private $sender;

    /**
     * @var DateTime
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var DateTime
     * @ORM\Column(name="time", type="time", nullable=false)
     */
    private $time;

    /**
     * @var string|null
     * @ORM\Column(name="comment", type="text", length=0, nullable=true)
     */
    private $comment;

    /**
     * @var ForumPostAlertNote[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumPostAlertNote", mappedBy="alert")
     */
    private $notes;

    /**
     *
     */
    public function __construct()
    {
        $this->notes = new ArrayCollection();
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
     * @return ForumPostAlert
     */
    public function setId(int $id): ForumPostAlert
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return ForumPost
     */
    public function getPost(): ForumPost
    {
        return $this->post;
    }

    /**
     * @param ForumPost $post
     * @return ForumPostAlert
     */
    public function setPost(ForumPost $post): ForumPostAlert
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->closed;
    }

    /**
     * @param bool $closed
     * @return ForumPostAlert
     */
    public function setClosed(bool $closed): ForumPostAlert
    {
        $this->closed = $closed;
        return $this;
    }

    /**
     * @return User
     */
    public function getSender(): User
    {
        return $this->sender;
    }

    /**
     * @param User $sender
     * @return ForumPostAlert
     */
    public function setSender(User $sender): ForumPostAlert
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return ForumPostAlert
     */
    public function setDate(DateTime $date): ForumPostAlert
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTime(): DateTime
    {
        return $this->time;
    }

    /**
     * @param DateTime $time
     * @return ForumPostAlert
     */
    public function setTime(DateTime $time): ForumPostAlert
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     * @return ForumPostAlert
     */
    public function setComment(?string $comment): ForumPostAlert
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @param ForumPostAlertNote $forumPostAlertNote
     * @return ForumPostAlert
     */
    public function addNote(ForumPostAlertNote $forumPostAlertNote): ForumPostAlert
    {
        $this->notes[] = $forumPostAlertNote;
        return $this;
    }

    /**
     * @return ForumPostAlertNote[]
     */
    public function getNotes(): array
    {
        return $this->notes->toArray();
    }

}
