<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_snb_spoor_nieuws_bron')]
class RailNewsSource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'snb_id', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'snb_bron', type: 'string', length: 7, nullable: false, options: ['default' => ''])]
    public string $name = '';

    #[ORM\Column(name: 'snb_logo', type: 'string', length: 25, nullable: false, options: ['default' => ''])]
    public string $logo = '';

    #[ORM\Column(name: 'snb_url', type: 'string', length: 30, nullable: false, options: ['default' => ''])]
    public string $url = '';

    #[ORM\Column(name: 'snb_description', type: 'string', length: 100, nullable: false, options: ['default' => ''])]
    public string $description = '';

    #[ORM\OneToMany(targetEntity: RailNewsSourceFeed::class, mappedBy: 'source')]
    private Collection $feeds;

    #[ORM\OneToMany(targetEntity: RailNews::class, mappedBy: 'source')]
    private Collection $news;

    public function __construct()
    {
        $this->feeds = new ArrayCollection();
        $this->news = new ArrayCollection();
    }

    public function addFeed(RailNewsSourceFeed $rail_news_source_feed): RailNewsSource
    {
        $this->feeds[] = $rail_news_source_feed;

        return $this;
    }

    /**
     * @return RailNewsSourceFeed[]
     */
    public function getFeeds(): array
    {
        return $this->feeds->toArray();
    }

    public function addNews(RailNews $rail_news): RailNewsSource
    {
        $this->news[] = $rail_news;

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
