<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_snb_spoor_nieuws_bron")
 * @ORM\Entity
 */
class RailNewsSource
{
    /**
     * @var int
     * @ORM\Column(name="snb_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="snb_bron", type="string", length=7, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="snb_logo", type="string", length=25, nullable=false)
     */
    private $logo;

    /**
     * @var string
     * @ORM\Column(name="snb_url", type="string", length=30, nullable=false)
     */
    private $url;

    /**
     * @var string
     * @ORM\Column(name="snb_description", type="string", length=100, nullable=false)
     */
    private $description;

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
        $this->news = new ArrayCollection();
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
     * @return RailNewsSource
     */
    public function setId(int $id): RailNewsSource
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return RailNewsSource
     */
    public function setName(string $name): RailNewsSource
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogo(): string
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     * @return RailNewsSource
     */
    public function setLogo(string $logo): RailNewsSource
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return RailNewsSource
     */
    public function setUrl(string $url): RailNewsSource
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return RailNewsSource
     */
    public function setDescription(string $description): RailNewsSource
    {
        $this->description = $description;
        return $this;
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
