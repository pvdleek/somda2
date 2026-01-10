<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BannerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BannerRepository::class)]
#[ORM\Table(name: 'somda_banner')]
class Banner
{
    public const LOCATION_HEADER = 'header';
    public const LOCATION_FORUM = 'forum';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'bannerid', type: 'smallint', options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(length: 6, nullable: true)]
    public ?string $code = null;

    #[ORM\Column(options: ['default' => false])]
    public bool $active = false;

    #[ORM\Column(length: 6, nullable: false, options: ['default' => self::LOCATION_HEADER])]
    public string $location = self::LOCATION_HEADER;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\Column(length: 255, nullable: false, options: ['default' => ''])]
    public string $link = '';

    #[ORM\Column(length: 100, nullable: true)]
    public ?string $image = null;

    #[ORM\Column(length: 50, nullable: false, options: ['default' => ''])]
    public string $email = '';

    #[ORM\Column(options: ['default' => 0])]
    public int $max_views = 0;

    #[ORM\Column(options: ['default' => 0])]
    public int $max_hits = 0;

    #[ORM\Column(name: 'start_date', type: 'datetime', nullable: true)]
    public ?\DateTime $start_timestamp;

    #[ORM\Column(name: 'end_date', type: 'datetime', nullable: true)]
    public ?\DateTime $end_timestamp;

    #[ORM\OneToMany(targetEntity: BannerHit::class, mappedBy: 'banner')]
    /** @var Collection<int, BannerHit> */
    private Collection $banner_hits;

    #[ORM\OneToMany(targetEntity: BannerView::class, mappedBy: 'banner')]
    /** @var Collection<int, BannerView> */
    private Collection $banner_views;

    #[ORM\ManyToOne(targetEntity: BannerCustomer::class, inversedBy: 'banners')]
    #[ORM\JoinColumn(name: 'customerid', referencedColumnName: 'id')]
    public BannerCustomer $customer;

    public function __construct()
    {
        $this->banner_hits = new ArrayCollection();
        $this->banner_views = new ArrayCollection();
    }

    public function addBannerHit(BannerHit $bannerHit): Banner
    {
        $this->banner_hits[] = $bannerHit;
        return $this;
    }

    /**
     * @return BannerHit[]
     */
    public function getBannerHits(): array
    {
        return $this->banner_hits->toArray();
    }

    public function addBannerView(BannerView $bannerView): Banner
    {
        $this->banner_views[] = $bannerView;
        return $this;
    }

    /**
     * @return BannerView[]
     */
    public function getBannerViews(): array
    {
        return $this->banner_views->toArray();
    }
}
