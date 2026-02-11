<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Banner;
use App\Repository\BannerRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-banner-statistics',
    description: 'Update statistics of all banners',
    hidden: false,
)]

class UpdateBannerStatisticsCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
    ) {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Banner[] $banners */

        // Activate banners that should start
        $banners = $this->doctrine->getRepository(Banner::class)->findBy(['active' => false]);
        foreach ($banners as $banner) {
            if ($banner->start_timestamp <= new \DateTime() && (null === $banner->end_timestamp || $banner->end_timestamp > new \DateTime())) {
                $banner->active = true;
            }
        }

        // De-activate banners that should stop
        /** @var BannerRepository $banner_repository */
        $banner_repository = $this->doctrine->getRepository(Banner::class);
        $banners = $banner_repository->findBy(['active' => true]);
        foreach ($banners as $banner) {
            $banner->views = $banner_repository->getNumberOfViews($banner);
            $banner->hits = $banner_repository->getNumberOfHits($banner);

            if (null !== $banner->end_timestamp && $banner->end_timestamp <= new \DateTime() || $banner->max_hits >= $banner->hits || $banner->max_views >= $banner->views) {
                $banner->active = false;
            }
        }

        $this->doctrine->getManager()->flush();

        return 0;
    }
}
