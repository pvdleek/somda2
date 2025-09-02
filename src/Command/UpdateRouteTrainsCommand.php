<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Position;
use App\Entity\Route;
use App\Entity\RouteTrain;
use App\Entity\Spot;
use App\Entity\TrainNamePattern;
use App\Entity\TrainTableYear;
use App\Repository\SpotRepository;
use App\Repository\TrainTableYearRepository;
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
        private readonly SpotRepository $spot_repository,
        private readonly TrainTableYearRepository $train_table_year_repository,
    ) {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var TrainTableYear $train_table_year
         */
        $train_table_year = $this->train_table_year_repository->findTrainTableYearByDate(new \DateTime());
        $check_date = max($train_table_year->start_date, new \DateTime('-'.self::CHECK_DATE_DAYS.' days'));

        $route_array = $this->spot_repository->findForRouteTrains($check_date);
        foreach ($route_array as $route_item) {
            /**
             * @var Route $route
             */
            $route = $this->doctrine->getRepository(Route::class)->find($route_item['route_id']);
            /**
             * @var TrainNamePattern $pattern
             */
            $pattern = $this->doctrine->getRepository(TrainNamePattern::class)->find($route_item['pattern_id']);
            /**
             * @var Position $position
             */
            $position = $this->doctrine->getRepository(Position::class)->find($route_item['position_id']);

            $route_train = $this->doctrine->getRepository(RouteTrain::class)->findOneBy([
                'train_table_year' => $train_table_year,
                'route' => $route,
                'position' => $position,
                'day_number' => $route_item['day_of_week'],
            ]);
            if (null === $route_train) {
                $route_train = new RouteTrain();
                $route_train->train_table_year = $train_table_year;
                $route_train->route = $route;
                $route_train->position = $position;
                $route_train->day_number = (int) $route_item['day_of_week'];

                $this->doctrine->getManager()->persist($route_train);
            }

            $route_train->number_of_spots = (int) $route_item['number_of_spots'];
            $route_train->train_name_pattern = $pattern;

            $this->doctrine->getManager()->flush();
        }

        return 0;
    }
}
