<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(name="somda_snb_spoor_nieuws_bron")
 * @ORM\Entity
 */
class RailNewsSource
{
    /**
     * @ORM\Column(name="snb_id", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="snb_bron", type="string", length=7, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="The abbreviation of the news-source", maxLength=7, type="string")
     */
    public string $name = '';

    /**
     * @ORM\Column(name="snb_logo", type="string", length=25, nullable=false)
     * @JMS\Exclude()
     */
    public string $logo = '';

    /**
     * @ORM\Column(name="snb_url", type="string", length=30, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="The base-URL of the news-source", maxLength=30, type="string")
     */
    public string $url = '';

    /**
     * @ORM\Column(name="snb_description", type="string", length=100, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="The name of the news-source", maxLength=100, type="string")
     */
    public string $description = '';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RailNewsSourceFeed", mappedBy="source")
     * @JMS\Exclude()
     */
    private $feeds;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RailNews", mappedBy="source")
     * @JMS\Exclude()
     */
    private $news;

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
