<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(name="somda_news")
 * @ORM\Entity(repositoryClass="App\Repository\News")
 */
class News
{
    /**
     * @ORM\Column(name="newsid", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the news-item (Y-m-dTH:i:sP)", type="string")
     */
    public ?\DateTime $timestamp = null;

    /**
     * @ORM\Column(name="title", type="string", length=50, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Title of the news-item", maxLength=50, type="string")
     */
    public string $title = '';

    /**
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Contents of the news-item", type="string")
     */
    public string $text = '';

    /**
     * @ORM\Column(name="archief", type="boolean", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Whether the news-item is in the archive", type="boolean")
     */
    public bool $archived = false;

    /**
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
