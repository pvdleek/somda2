<?php

namespace App\Command;

use App\Helpers\OfficialTrainTableHelper;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
        $this->trainTableHelper->processIffFiles();

        return 0;
    }
}
