<?php

namespace App\Command;

use AurimasNiekis\SchedulerBundle\ScheduledJobInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateLocationsCommand extends Command implements ScheduledJobInterface
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:update-locations';

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
        return '3 0 * * 0';
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setDescription('Update the locations from the official list at NS');
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
