<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Position;
use App\Entity\Route;
use App\Entity\RouteTrain;
use App\Entity\Spot;
use App\Entity\TrainNamePattern;
use App\Entity\TrainTableYear;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-route-trains',
    description: 'Update route-trains',
    hidden: false,
)]

class UpdateRouteTrainsCommand extends Command
{
    private const CHECK_DATE_DAYS = 300;

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
         * @var TrainTableYear $trainTableYear
         */
        $trainTableYear = $this->doctrine
            ->getRepository(TrainTableYear::class)
            ->findTrainTableYearByDate(new \DateTime());
        $checkDate = max($trainTableYear->startDate, new \DateTime('-' . self::CHECK_DATE_DAYS . ' days'));

        $routeArray = $this->doctrine->getRepository(Spot::class)->findForRouteTrains($checkDate);
        foreach ($routeArray as $routeItem) {
            /**
             * @var Route $route
             */
            $route = $this->doctrine->getRepository(Route::class)->find($routeItem['routeId']);
            /**
             * @var TrainNamePattern $pattern
             */
            $pattern = $this->doctrine->getRepository(TrainNamePattern::class)->find($routeItem['patternId']);
            /**
             * @var Position $position
             */
            $position = $this->doctrine->getRepository(Position::class)->find($routeItem['positionId']);

            $routeTrain = $this->doctrine->getRepository(RouteTrain::class)->findOneBy([
                'trainTableYear' => $trainTableYear,
                'route' => $route,
                'position' => $position,
                'dayNumber' => $routeItem['dayOfWeek'],
            ]);
            if (null === $routeTrain) {
                $routeTrain = new RouteTrain();
                $routeTrain->trainTableYear = $trainTableYear;
                $routeTrain->route = $route;
                $routeTrain->position = $position;
                $routeTrain->dayNumber = (int) $routeItem['dayOfWeek'];

                $this->doctrine->getManager()->persist($routeTrain);
            }

            $routeTrain->numberOfSpots = (int) $routeItem['numberOfSPots'];
            $routeTrain->trainNamePattern = $pattern;

            $this->doctrine->getManager()->flush();
        }

        return 0;
    }
}
