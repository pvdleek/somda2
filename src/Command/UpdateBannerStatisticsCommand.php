<?php

namespace App\Command;

use App\Entity\Banner;
use AurimasNiekis\SchedulerBundle\ScheduledJobInterface;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateBannerStatisticsCommand extends Command implements ScheduledJobInterface
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
    public function __invoke()
    {
        $this->execute();
    }

    /**
     * @return string
     */
    public function getSchedulerExpresion(): string
    {
        return '3,18,33,48 * * * *';
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
            if ($banner->startTimestamp <= new DateTime() &&
                (is_null($banner->endTimestamp) || $banner->endTimestamp > new DateTime())
            ) {
                $banner->active = true;
            }
        }

        // De-activate banners that should stop
        $banners = $this->doctrine->getRepository(Banner::class)->findBy(['active' => true]);
        foreach ($banners as $banner) {
            if (!is_null($banner->endTimestamp) && $banner->endTimestamp <= new DateTime() ||
                $banner->maxHits >= count($banner->getBannerHits()) ||
                $banner->maxViews >= count($banner->getBannerViews())
            ) {
                $banner->active = false;
            }
        }

        $this->doctrine->getManager()->flush();

        return 0;
    }
}
