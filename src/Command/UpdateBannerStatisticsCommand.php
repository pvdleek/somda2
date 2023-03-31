<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Banner;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateBannerStatisticsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:update-banner-statistics';

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        parent::__construct(self::$defaultName);

        $this->doctrine = $doctrine;
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setDescription('Update statistics of all banners');
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input = null, OutputInterface $output = null): int
    {
        /**
         * @var Banner[] $banners
         */

        // Activate banners that should start
        $banners = $this->doctrine->getRepository(Banner::class)->findBy(['active' => false]);
        foreach ($banners as $banner) {
            if ($banner->startTimestamp <= new \DateTime() &&
                (null === $banner->endTimestamp || $banner->endTimestamp > new \DateTime())
            ) {
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
