<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_sns_spoor_nieuws")
 * @ORM\Entity
 */
class RailNews
{
    /**
     * @var int
     *
     * @ORM\Column(name="sns_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="sns_bron", type="string", length=7, nullable=false)
     */
    private $source;

    /**
     * @var string
     *
     * @ORM\Column(name="sns_titel", type="string", length=100, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="sns_url", type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="sns_introductie", type="text", length=0, nullable=false)
     */
    private $introduction;

    /**
     * @var int
     *
     * @ORM\Column(name="sns_datumtijd", type="bigint", nullable=false)
     */
    private $timestamp;

    /**
     * @var int
     *
     * @ORM\Column(name="sns_gekeurd", type="bigint", nullable=false)
     */
    private $approved = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="sns_actief", type="bigint", nullable=false, options={"default"="1"})
     */
    private $active = '1';

    /**
     * @var int
     *
     * @ORM\Column(name="sns_bijwerken_ok", type="bigint", nullable=false, options={"default"="1"})
     */
    private $automaticUpdates = '1';

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return RailNews
     */
    public function setId(int $id): RailNews
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return RailNews
     */
    public function setSource(string $source): RailNews
    {
        $this->source = $source;
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
     * @return RailNews
     */
    public function setTitle(string $title): RailNews
    {
        $this->title = $title;
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
     * @return RailNews
     */
    public function setUrl(string $url): RailNews
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getIntroduction(): string
    {
        return $this->introduction;
    }

    /**
     * @param string $introduction
     * @return RailNews
     */
    public function setIntroduction(string $introduction): RailNews
    {
        $this->introduction = $introduction;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     * @return RailNews
     */
    public function setTimestamp(int $timestamp): RailNews
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return int
     */
    public function getApproved(): int
    {
        return $this->approved;
    }

    /**
     * @param int $approved
     * @return RailNews
     */
    public function setApproved(int $approved): RailNews
    {
        $this->approved = $approved;
        return $this;
    }

    /**
     * @return int
     */
    public function getActive(): int
    {
        return $this->active;
    }

    /**
     * @param int $active
     * @return RailNews
     */
    public function setActive(int $active): RailNews
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return int
     */
    public function getAutomaticUpdates(): int
    {
        return $this->automaticUpdates;
    }

    /**
     * @param int $automaticUpdates
     * @return RailNews
     */
    public function setAutomaticUpdates(int $automaticUpdates): RailNews
    {
        $this->automaticUpdates = $automaticUpdates;
        return $this;
    }
}
