<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

#[ORM\Entity(repositoryClass: NewsRepository::class)]
#[ORM\Table(name: 'somda_news')]
class News
{
    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'newsid', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the news-item (Y-m-dTH:i:sP)", type="string")
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTime $timestamp = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Title of the news-item", maxLength=50, type="string")
     */
    #[ORM\Column(length: 50, nullable: false, options: ['default' => ''])]
    public string $title = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="Contents of the news-item", type="string")
     */
    #[ORM\Column(type: 'text', nullable: false, options: ['default' => ''])]
    public string $text = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="Whether the news-item is in the archive", type="boolean")
     */
    #[ORM\Column(name: 'archief', nullable: false, options: ['default' => false])]
    public bool $archived = false;

    /**
     * @JMS\Exclude()
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'news_reads')]
    #[ORM\JoinTable(name: 'somda_news_read', 
        joinColumns: [new ORM\JoinColumn(name: 'newsid', referencedColumnName: 'newsid')], 
        inverseJoinColumns: [new ORM\JoinColumn(name: 'uid', referencedColumnName: 'uid')]
    )]
    private $user_reads;

    /**
     *
     */
    public function __construct()
    {
        $this->user_reads = new ArrayCollection();
    }

    public function addUserRead(User $user): News
    {
        $this->user_reads[] = $user;
        return $this;
    }

    /**
     * @return User[]
     */
    public function getUserReads(): array
    {
        return $this->user_reads->toArray();
    }
}
