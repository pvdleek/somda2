<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Train;
use App\Entity\TrainNamePattern;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LinkTrainsToNamingPatternCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:link-trains-to-naming-pattern';

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
        $this->setDescription('Link all trains (somda_mat) to naming patterns');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var TrainNamePattern[] $patterns
         * @var Train[] $trains
         */
        // Get all naming patterns
        $patterns = $this->doctrine->getRepository(TrainNamePattern::class)->findBy([], ['order' => 'ASC']);

        // Get all trains
        $trains = $this->doctrine->getRepository(Train::class)->findAll();

        foreach ($trains as $train) {
            foreach ($patterns as $pattern) {
                if (preg_match('#' . $pattern->pattern . '#', $train->number)) {
                    $train->namePattern = $pattern;
                    break;
                }
            }
        }
        $this->doctrine->getManager()->flush();

        return 0;
    }
}
