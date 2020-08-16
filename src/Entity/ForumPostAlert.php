<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_alerts", indexes={@ORM\Index(name="idx_47886_postid", columns={"postid"})})
 * @ORM\Entity(repositoryClass="App\Repository\ForumPostAlert")
 */
class ForumPostAlert
{
    /**
     * @var int|null
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var ForumPost
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost", inversedBy="alerts")
     * @ORM\JoinColumn(name="postid", referencedColumnName="postid")
     */
    public ForumPost $post;

    /**
     * @var bool
     * @ORM\Column(name="closed", type="boolean", nullable=false)
     */
    public bool $closed = false;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="senderid", referencedColumnName="uid")
     */
    public User $sender;

    /**
     * @var DateTime
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var string|null
     * @ORM\Column(name="comment", type="text", length=0, nullable=true)
     */
    public ?string $comment;

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
