<?php

namespace App\Command;

use AurimasNiekis\SchedulerBundle\ScheduledJobInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetRailNewsCommand extends Command implements ScheduledJobInterface
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:get-rail-news';

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
        return '14,29,44,59 * * * *';
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setDescription('Read all the rail-news providers and process the news items');
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     */
    protected function execute(InputInterface $input = null, OutputInterface $output = null): int
    {


        return 0;
    }
}
