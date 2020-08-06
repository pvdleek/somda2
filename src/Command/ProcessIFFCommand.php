<?php

namespace App\Command;

use App\Helpers\OfficialTrainTableHelper;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessIFFCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:process-iff';

    /**
     * @var OfficialTrainTableHelper
     */
    private OfficialTrainTableHelper $trainTableHelper;

    /**
     * @param OfficialTrainTableHelper $trainTableHelper
     */
    public function __construct(OfficialTrainTableHelper $trainTableHelper)
    {
        parent::__construct(self::$defaultName);

        $this->trainTableHelper = $trainTableHelper;
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->addArgument('directory', InputArgument::REQUIRED)
            ->addOption('footnotes', 'f', InputOption::VALUE_NONE, 'Process the footnotes')
            ->addOption('companies', 'c', InputOption::VALUE_NONE, 'Process the companies')
            ->addOption('characteristics', 'ch', InputOption::VALUE_NONE, 'Process the characteristics')
            ->addOption('stations', 's', InputOption::VALUE_NONE, 'Process the stations')
            ->addOption('train-tables', 't', InputOption::VALUE_NONE, 'Process the train-tables')
            ->setDescription('Process the IFF files from NS');
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input = null, OutputInterface $output = null): int
    {
        $this->trainTableHelper->setDirectory($input->getArgument('directory'));

        if ($input->getOption('footnotes') === true) {
            $this->trainTableHelper->processFootnotes();
        }
        if ($input->getOption('companies') === true) {
            $this->trainTableHelper->processCompanies();
        }
        if ($input->getOption('characteristics') === true) {
            $this->trainTableHelper->processCharacteristics();
        }
        if ($input->getOption('stations') === true) {
            $this->trainTableHelper->processStations();
        }
        if ($input->getOption('train-tables') === true) {
            $this->trainTableHelper->processTrainTables();
        }

        return 0;
    }
}
