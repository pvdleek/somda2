<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_sns_spoor_nieuws")
 * @ORM\Entity
 */
class RailNews
{
    /**
     * @var int
     * @ORM\Column(name="sns_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="sns_titel", type="string", length=100, nullable=false)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="sns_url", type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * @var string
     * @ORM\Column(name="sns_introductie", type="text", length=0, nullable=false)
     */
    private $introduction;

    /**
     * @var DateTime
     * @ORM\Column(name="sns_timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var boolean
     * @ORM\Column(name="sns_gekeurd", type="boolean", nullable=false)
     */
    private $approved = false;

    /**
     * @var boolean
     * @ORM\Column(name="sns_actief", type="boolean", nullable=false, options={"default"="1"})
     */
    private $active = true;

    /**
     * @var boolean
     * @ORM\Column(name="sns_bijwerken_ok", type="boolean", nullable=false, options={"default"="1"})
     */
    private $automaticUpdates = true;

    /**
     * @var RailNewsSource
     * @ORM\ManyToOne(targetEntity="App\Entity\RailNewsSource", inversedBy="news")
     * @ORM\JoinColumn(name="sns_snb_id", referencedColumnName="snb_id")
     */
    private $source;

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
     * @return DateTime
     */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param DateTime $timestamp
     * @return RailNews
     */
    public function setTimestamp(DateTime $timestamp): RailNews
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->approved;
    }

    /**
     * @param bool $approved
     * @return RailNews
     */
    public function setApproved(bool $approved): RailNews
    {
        $this->approved = $approved;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return RailNews
     */
    public function setActive(bool $active): RailNews
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAutomaticUpdates(): bool
    {
        return $this->automaticUpdates;
    }

    /**
     * @param bool $automaticUpdates
     * @return RailNews
     */
    public function setAutomaticUpdates(bool $automaticUpdates): RailNews
    {
        $this->automaticUpdates = $automaticUpdates;
        return $this;
    }

    /**
     * @return RailNewsSource
     */
    public function getSource(): RailNewsSource
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getLogo(): string
    {
        list($width, $height) = $this->resizeImage(
            getimagesize(__DIR__ . '/../../public/images/news-logos/' . $this->getSource()->getLogo()),
            [500, 25]
        );
        return '<a href="http://' . $this->getSource()->getUrl() . '/" target="_blank"><img alt="' .
            $this->getSource()->getDescription() . '" src="/images/news-logos/' .
            $this->getSource()->getLogo() . '" height="' . $height . '" width="' . $width . '"  /></a>';
    }

    /**
     * This function calculates thumbnail width and height for an image
     * @param array $currentSizes - an array with 2 values: 0 = width, 1 = height
     * @param array $maxSizes - an array with 2 values: 0 = width, 1 = height
     * @return array - an array with 2 values: 0 = width, 1 = height
     */
    private function resizeImage(array $currentSizes, array $maxSizes): array {
        if (($currentSizes[0] <= $maxSizes[0]) && ($currentSizes[1] <= $maxSizes[1])) {
            return [$currentSizes[0], $currentSizes[1]];
        }

        $xRatio = $maxSizes[0] / $currentSizes[0];
        if (($xRatio * $currentSizes[1]) < $maxSizes[1]) {
            return [$maxSizes[0], ceil($xRatio * $currentSizes[1])];
        }

        $yRatio = $maxSizes[1] / $currentSizes[1];
        return [ceil($yRatio * $currentSizes[0]), $maxSizes[1]];
    }

    /**
     * @param RailNewsSource $source
     * @return RailNews
     */
    public function setSource(RailNewsSource $source): RailNews
    {
        $this->source = $source;
        return $this;
    }
}
