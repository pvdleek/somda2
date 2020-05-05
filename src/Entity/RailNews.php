<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_sns_spoor_nieuws")
 * @ORM\Entity
 */
class RailNews extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="sns_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="sns_titel", type="string", length=100, nullable=false)
     */
    public string $title;

    /**
     * @var string
     * @ORM\Column(name="sns_url", type="string", length=255, nullable=false)
     */
    public string $url;

    /**
     * @var string
     * @ORM\Column(name="sns_introductie", type="text", length=0, nullable=false)
     */
    public string $introduction;

    /**
     * @var DateTime
     * @ORM\Column(name="sns_timestamp", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var bool
     * @ORM\Column(name="sns_gekeurd", type="boolean", nullable=false)
     */
    public bool $approved = false;

    /**
     * @var bool
     * @ORM\Column(name="sns_actief", type="boolean", nullable=false, options={"default"="1"})
     */
    public bool $active = true;

    /**
     * @var bool
     * @ORM\Column(name="sns_bijwerken_ok", type="boolean", nullable=false, options={"default"="1"})
     */
    public bool $automaticUpdates = true;

    /**
     * @var RailNewsSource
     * @ORM\ManyToOne(targetEntity="App\Entity\RailNewsSource", inversedBy="news")
     * @ORM\JoinColumn(name="sns_snb_id", referencedColumnName="snb_id")
     */
    public RailNewsSource $source;

    /**
     * @return string
     */
    public function getLogo(): string
    {
        list($width, $height) = $this->resizeImage(
            getimagesize(__DIR__ . '/../../public/images/news-logos/' . $this->source->logo),
            [500, 25]
        );
        return '<a href="http://' . $this->source->url . '/" target="_blank"><img alt="' .
            $this->source->description . '" src="/images/news-logos/' .
            $this->source->logo . '" height="' . $height . '" width="' . $width . '"  /></a>';
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
}
