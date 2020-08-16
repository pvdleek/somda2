<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(name="somda_news")
 * @ORM\Entity(repositoryClass="App\Repository\News")
 */
class News
{
    /**
     * @var int|null
     * @ORM\Column(name="newsid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var DateTime
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="ISO-8601 timestamp of the news-item (Y-m-dTH:i:sP)", type="string")
     */
    public DateTime $timestamp;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=50, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Title of the news-item", maxLength=50, type="string")
     */
    public string $title = '';

    /**
     * @var string
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Contents of the news-item", type="string")
     */
    public string $text = '';

    /**
     * @var bool
     * @ORM\Column(name="archief", type="boolean", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Whether the news-item is in the archive", type="boolean")
     */
    public bool $archived = false;

    /**
     * @var User[]
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="somda_news_read",
     *      joinColumns={@ORM\JoinColumn(name="newsid", referencedColumnName="newsid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="uid", referencedColumnName="uid")}
     * )
     * @JMS\Exclude()
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
