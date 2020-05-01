<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_snb_spoor_nieuws_bron")
 * @ORM\Entity
 */
class RailNewsSource extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="snb_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected int $id;

    /**
     * @var string
     * @ORM\Column(name="snb_bron", type="string", length=7, nullable=false)
     */
    public string $name;

    /**
     * @var string
     * @ORM\Column(name="snb_logo", type="string", length=25, nullable=false)
     */
    public string $logo;

    /**
     * @var string
     * @ORM\Column(name="snb_url", type="string", length=30, nullable=false)
     */
    public string $url;

    /**
     * @var string
     * @ORM\Column(name="snb_description", type="string", length=100, nullable=false)
     */
    public string $description;

    /**
     * @var RailNewsSourceFeed
     * @ORM\OneToMany(targetEntity="App\Entity\RailNewsSourceFeed", mappedBy="source")
     */
    private $feeds;

    /**
     * @var RailNews
     * @ORM\OneToMany(targetEntity="App\Entity\RailNews", mappedBy="source")
     */
    private $news;

    /**
     *
     */
    public function __construct()
    {
        $this->feeds = new ArrayCollection();
        $this->news = new ArrayCollection();
    }

    /**
     * @param RailNewsSourceFeed $railNewsSourceFeed
     * @return RailNewsSource
     */
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

    /**
     * @param RailNews $railNews
     * @return RailNewsSource
     */
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
