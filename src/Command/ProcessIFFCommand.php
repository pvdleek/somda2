<?php

namespace App\Command;

use App\Helpers\OfficialTrainTableHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:process-iff',
    description: 'Process the IFF files from NS via the "NDOV loket"',
    hidden: false,
)]

class ProcessIFFCommand extends Command
{
    public function __construct(
        private readonly OfficialTrainTableHelper $trainTableHelper,
    ) {
        parent::__construct();
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->addOption('footnotes', 'f', InputOption::VALUE_NONE, 'Process the footnotes')
            ->addOption('companies', 'c', InputOption::VALUE_NONE, 'Process the companies')
            ->addOption('characteristics', 'a', InputOption::VALUE_NONE, 'Process the characteristics')
            ->addOption('stations', 's', InputOption::VALUE_NONE, 'Process the stations')
            ->addOption('train-tables', 't', InputOption::VALUE_NONE, 'Process the train-tables');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input = null, OutputInterface $output = null): int
    {
        // Get the ZIP from the NDOV loket and extract it to the directory
        $this->getZipFile('https://data.ndovloket.nl/ns/ns-latest.zip');
        $this->trainTableHelper->setDirectory('/tmp');

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

    private function getZipFile(string $zip_file): void
    {
        \file_put_contents('/tmp/iff.zip', \file_get_contents($zip_file));

        $zip = new \ZipArchive();
        $zip->open('/tmp/iff.zip');
        $zip->extractTo('/tmp');
        $zip->close();
    }
}
