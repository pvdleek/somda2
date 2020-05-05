<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_news")
 * @ORM\Entity(repositoryClass="App\Repository\News")
 */
class News extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="newsid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var DateTime
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=50, nullable=false)
     */
    public string $title = '';

    /**
     * @var string
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     */
    public string $text = '';

    /**
     * @var bool
     * @ORM\Column(name="archief", type="boolean", nullable=false)
     */
    public bool $archived = false;

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
