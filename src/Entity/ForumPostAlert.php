<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fpa_forum_post_alert", indexes={
 *     @ORM\Index(name="IDX_fpa_fop_id", columns={"fpa_fop_id"}),
 *     @ORM\Index(name="IDX_fpa_sender_use_id", columns={"fpa_sender_use_id"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\ForumPostAlert")
 */
class ForumPostAlert
{
    /**
     * @var int|null
     * @ORM\Column(name="fpa_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var ForumPost
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost", inversedBy="alerts")
     * @ORM\JoinColumn(name="fpa_fop_id", referencedColumnName="fop_id")
     */
    public ForumPost $post;

    /**
     * @var bool
     * @ORM\Column(name="fpa_closed", type="boolean", nullable=false)
     */
    public bool $closed = false;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="fpa_sender_use_id", referencedColumnName="use_id")
     */
    public User $sender;

    /**
     * @var DateTime
     * @ORM\Column(name="fpa_timestamp", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var string|null
     * @ORM\Column(name="fpa_comment", type="text", length=0, nullable=true)
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
