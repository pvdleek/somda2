<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RailNewsRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

#[ORM\Entity(repositoryClass: RailNewsRepository::class)]
#[ORM\Table(name: 'somda_sns_spoor_nieuws')]
class RailNews
{
    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'sns_id', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Title of the news-item", maxLength=255, type="string")
     */
    #[ORM\Column(name: 'sns_titel', length: 255, nullable: false, options: ['default' => ''])]
    public string $title = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="URL of the news-item at the source", maxLength=255, type="string")
     */
    #[ORM\Column(name: 'sns_url', length: 255, nullable: false, options: ['default' => ''])]
    public string $url = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="Introductionary text for the news-item", type="string")
     */
    #[ORM\Column(name: 'sns_introductie', type: 'text', nullable: false, options: ['default' => ''])]
    public string $introduction = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the news-item (Y-m-dTH:i:sP)", type="string")
     */
    #[ORM\Column(name: 'sns_timestamp', type: 'datetime', nullable: true)]
    public ?\DateTime $timestamp = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\Column(name: 'sns_gekeurd', nullable: false, options: ['default' => false])]
    public bool $approved = false;

    /**
     * @JMS\Exclude()
     */
    #[ORM\Column(name: 'sns_actief', nullable: false, options: ['default' => true])]
    public bool $active = true;

    /**
     * @JMS\Exclude()
     */
    #[ORM\Column(name: 'sns_bijwerken_ok', nullable: false, options: ['default' => true])]
    public bool $automatic_updates = true;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The source of the news-item", ref=@Model(type=RailNewsSource::class))
     */
    #[ORM\ManyToOne(targetEntity: RailNewsSource::class, inversedBy: 'news')]
    #[ORM\JoinColumn(name: 'sns_snb_id', referencedColumnName: 'snb_id')]
    public ?RailNewsSource $source = null;

    public function getLogo(): string
    {
        list($width, $height) = $this->resizeImage(
            \getimagesize(__DIR__.'/../../html/images/news-logos/'.$this->source->logo),
            [500, 25]
        );
        return '<a href="http://'.$this->source->url.'/" target="_blank"><img alt="' .
            $this->source->description.'" src="/images/news-logos/' .
            $this->source->logo.'" height="'.$height.'" width="'.$width.'"  /></a>';
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
            return [$maxSizes[0], \ceil($xRatio * $currentSizes[1])];
        }

        $yRatio = $maxSizes[1] / $currentSizes[1];
        return [\ceil($yRatio * $currentSizes[0]), $maxSizes[1]];
    }
}
