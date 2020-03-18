<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_alerts_notes", indexes={@ORM\Index(name="idx_47898_alertid", columns={"alertid"})})
 * @ORM\Entity
 */
class ForumPostAlertNote
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var ForumPostAlert
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPostAlert", inversedBy="notes")
     * @ORM\JoinColumn(name="alertid", referencedColumnName="id")
     */
    private $alert;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="authorid", referencedColumnName="uid")
     */
    private $author;

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
     * @var boolean
     * @ORM\Column(name="sent_to_reporter", type="boolean", nullable=false)
     */
    private $sentToReporter;

    /**
     * @var string
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     */
    private $text;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ForumPostAlertNote
     */
    public function setId(int $id): ForumPostAlertNote
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return ForumPostAlert
     */
    public function getAlert(): ForumPostAlert
    {
        return $this->alert;
    }

    /**
     * @param ForumPostAlert $alert
     * @return ForumPostAlertNote
     */
    public function setAlert(ForumPostAlert $alert): ForumPostAlertNote
    {
        $this->alert = $alert;
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
     * @return ForumPostAlertNote
     */
    public function setAuthor(User $author): ForumPostAlertNote
    {
        $this->author = $author;
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
     * @return ForumPostAlertNote
     */
    public function setDate(DateTime $date): ForumPostAlertNote
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
     * @return ForumPostAlertNote
     */
    public function setTime(DateTime $time): ForumPostAlertNote
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSentToReporter(): bool
    {
        return $this->sentToReporter;
    }

    /**
     * @param bool $sentToReporter
     * @return ForumPostAlertNote
     */
    public function setSentToReporter(bool $sentToReporter): ForumPostAlertNote
    {
        $this->sentToReporter = $sentToReporter;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return ForumPostAlertNote
     */
    public function setText(string $text): ForumPostAlertNote
    {
        $this->text = $text;
        return $this;
    }
}
