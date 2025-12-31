<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RailNewsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RailNewsRepository::class)]
#[ORM\Table(name: 'somda_sns_spoor_nieuws')]
class RailNews
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'sns_id', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'sns_titel', length: 255, nullable: false, options: ['default' => ''])]
    public string $title = '';

    #[ORM\Column(name: 'sns_url', length: 255, nullable: false, options: ['default' => ''])]
    public string $url = '';

    #[ORM\Column(name: 'sns_introductie', type: 'text', nullable: false, options: ['default' => ''])]
    public string $introduction = '';

    #[ORM\Column(name: 'sns_timestamp', type: 'datetime', nullable: true)]
    public ?\DateTime $timestamp = null;

    #[ORM\Column(name: 'sns_gekeurd', nullable: false, options: ['default' => false])]
    public bool $approved = false;

    #[ORM\Column(name: 'sns_actief', nullable: false, options: ['default' => true])]
    public bool $active = true;

    #[ORM\Column(name: 'sns_bijwerken_ok', nullable: false, options: ['default' => true])]
    public bool $automatic_updates = true;

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
