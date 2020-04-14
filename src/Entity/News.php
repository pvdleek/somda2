<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_news")
 * @ORM\Entity(repositoryClass="App\Repository\News")
 */
class News
{
    /**
     * @var int
     * @ORM\Column(name="newsid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var DateTime
     * @ORM\Column(name="datum", type="date", nullable=false)
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=50, nullable=false)
     */
    private $title = '';

    /**
     * @var string
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     */
    private $text;

    /**
     * @var boolean
     * @ORM\Column(name="archief", type="boolean", nullable=false)
     */
    private $archived = false;

    /**
     * @var DateTime
     * @ORM\Column(name="timestamp", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $timestamp = 'CURRENT_TIMESTAMP';

    /**
     * @var User[]
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="somda_news_read",
     *      joinColumns={@ORM\JoinColumn(name="newsid", referencedColumnName="newsid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="uid", referencedColumnName="uid")}
     * )
     */
    private $userReads;

    /**
     *
     */
    public function __construct()
    {
        $this->userReads = new ArrayCollection();
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
     * @return News
     */
    public function setId(int $id): News
    {
        $this->id = $id;
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
     * @return News
     */
    public function setDate(DateTime $date): News
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return News
     */
    public function setTitle(string $title): News
    {
        $this->title = $title;
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
     * @return News
     */
    public function setText(string $text): News
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->archived;
    }

    /**
     * @param bool $archived
     * @return News
     */
    public function setArchived(bool $archived): News
    {
        $this->archived = $archived;
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
     * @return News
     */
    public function setTimestamp(DateTime $timestamp): News
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @param User $user
     * @return News
     */
    public function addUserRead(User $user): News
    {
        $this->userReads[] = $user;
        return $this;
    }

    /**
     * @return User[]
     */
    public function getUserReads(): array
    {
        return $this->userReads->toArray();
    }
}
