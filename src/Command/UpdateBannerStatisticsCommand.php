<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Banner;
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
    protected function execute(InputInterface $input = null, OutputInterface $output = null): int
    {
        /**
         * @var Banner[] $banners
         */

        // Activate banners that should start
        $banners = $this->doctrine->getRepository(Banner::class)->findBy(['active' => false]);
        foreach ($banners as $banner) {
            if ($banner->startTimestamp <= new \DateTime() && (null === $banner->endTimestamp || $banner->endTimestamp > new \DateTime())) {
                $banner->active = true;
            }
        }

        // De-activate banners that should stop
        $banners = $this->doctrine->getRepository(Banner::class)->findBy(['active' => true]);
        foreach ($banners as $banner) {
            if (null !== $banner->endTimestamp && $banner->endTimestamp <= new \DateTime() ||
                $banner->maxHits >= \count($banner->getBannerHits()) ||
                $banner->maxViews >= \count($banner->getBannerViews())
            ) {
                $banner->active = false;
            }
        }

        $this->doctrine->getManager()->flush();

        return 0;
    }
}
