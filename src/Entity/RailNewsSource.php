<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

#[ORM\Entity]
#[ORM\Table(name: 'somda_snb_spoor_nieuws_bron')]
class RailNewsSource
{
    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'snb_id', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The abbreviation of the news-source", maxLength=7, type="string")
     */
    #[ORM\Column(name: 'snb_bron', type: 'string', length: 7, nullable: false, options: ['default' => ''])]
    public string $name = '';

    /**
     * @JMS\Exclude()
     */
    #[ORM\Column(name: 'snb_logo', type: 'string', length: 25, nullable: false, options: ['default' => ''])]
    public string $logo = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="The base-URL of the news-source", maxLength=30, type="string")
     */
    #[ORM\Column(name: 'snb_url', type: 'string', length: 30, nullable: false, options: ['default' => ''])]
    public string $url = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="The name of the news-source", maxLength=100, type="string")
     */
    #[ORM\Column(name: 'snb_description', type: 'string', length: 100, nullable: false, options: ['default' => ''])]
    public string $description = '';

    /**
     * @JMS\Exclude()
     */
    #[ORM\OneToMany(targetEntity: RailNewsSourceFeed::class, mappedBy: 'source')]
    private Collection $feeds;

    /**
     * @JMS\Exclude()
     */
    #[ORM\OneToMany(targetEntity: RailNews::class, mappedBy: 'source')]
    private Collection $news;

    public function __construct()
    {
        $this->feeds = new ArrayCollection();
        $this->news = new ArrayCollection();
    }

    public function addFeed(RailNewsSourceFeed $railNewsSourceFeed): RailNewsSource
    {
        $this->feeds[] = $railNewsSourceFeed;
        return $this;
    }

    /**
     * @return RailNewsSourceFeed[]
     */
    public function getFeeds(): array
    {
        return $this->feeds->toArray();
    }

    public function addNews(RailNews $railNews): RailNewsSource
    {
        $this->news[] = $railNews;
        return $this;
    }

    /**
     * @return RailNews[]
     */
    public function getNews(): array
    {
        return $this->news->toArray();
    }
}
