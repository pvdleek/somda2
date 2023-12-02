<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Train;
use App\Entity\TrainNamePattern;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:link-trains-to-naming-pattern',
    description: 'Link all trains (somda_mat) to naming patterns',
    hidden: false,
)]

class LinkTrainsToNamingPatternCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var TrainNamePattern[] $patterns
         */
        $patterns = $this->doctrine->getRepository(TrainNamePattern::class)->findBy([], ['order' => 'ASC']);
        /**
         * @var Train[] $trains
         */
        $trains = $this->doctrine->getRepository(Train::class)->findAll();

        foreach ($trains as $train) {
            foreach ($patterns as $pattern) {
                if (\preg_match('#' . $pattern->pattern . '#', $train->number)) {
                    $train->namePattern = $pattern;
                    break;
                }
            }
        }
        $this->doctrine->getManager()->flush();

        return 0;
    }
}
