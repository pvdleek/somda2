<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(name="somda_sns_spoor_nieuws")
 * @ORM\Entity(repositoryClass="App\Repository\RailNews")
 */
class RailNews
{
    /**
     * @var int|null
     * @ORM\Column(name="sns_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="sns_titel", type="string", length=100, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Title of the news-item", maxLength=100, type="string")
     */
    public string $title;

    /**
     * @var string
     * @ORM\Column(name="sns_url", type="string", length=255, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="URL of the news-item at the source", maxLength=255, type="string")
     */
    public string $url;

    /**
     * @var string
     * @ORM\Column(name="sns_introductie", type="text", length=0, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Introductionary text for the news-item", type="string")
     */
    public string $introduction;

    /**
     * @var DateTime
     * @ORM\Column(name="sns_timestamp", type="datetime", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="ISO-8601 timestamp of the news-item (Y-m-dTH:i:sP)", type="string")
     */
    public DateTime $timestamp;

    /**
     * @var bool
     * @ORM\Column(name="sns_gekeurd", type="boolean", nullable=false)
     * @JMS\Exclude()
     */
    public bool $approved = false;

    /**
     * @var bool
     * @ORM\Column(name="sns_actief", type="boolean", nullable=false, options={"default"="1"})
     * @JMS\Exclude()
     */
    public bool $active = true;

    /**
     * @var bool
     * @ORM\Column(name="sns_bijwerken_ok", type="boolean", nullable=false, options={"default"="1"})
     * @JMS\Exclude()
     */
    public bool $automaticUpdates = true;

    /**
     * @var RailNewsSource
     * @ORM\ManyToOne(targetEntity="App\Entity\RailNewsSource", inversedBy="news")
     * @ORM\JoinColumn(name="sns_snb_id", referencedColumnName="snb_id")
     * @JMS\Expose()
     * @SWG\Property(description="The source of the news-item", ref=@Model(type=RailNewsSource::class))
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
    private function resizeImage(array $currentSizes, array $maxSizes): array
    {
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
