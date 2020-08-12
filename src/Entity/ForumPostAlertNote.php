<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_alerts_notes", indexes={@ORM\Index(name="idx_47898_alertid", columns={"alertid"})})
 * @ORM\Entity
 */
class ForumPostAlertNote extends Entity
{
    /**
     * @var int|null
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var ForumPostAlert
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPostAlert", inversedBy="notes")
     * @ORM\JoinColumn(name="alertid", referencedColumnName="id")
     */
    public ForumPostAlert $alert;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="authorid", referencedColumnName="uid")
     */
    public User $author;

    /**
     * @var DateTime
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var bool
     * @ORM\Column(name="sent_to_reporter", type="boolean", nullable=false)
     */
    public bool $sentToReporter;

    /**
     * @var string
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     */
    public string $text;
}
