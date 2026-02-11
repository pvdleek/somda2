<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\TrainsForForum;
use App\Entity\TrainTableYear;
use App\Repository\TrainTableRepository;
use App\Repository\TrainTableYearRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-trains-for-forum',
    description: 'Update trains to be used in the forum',
    hidden: false,
)]

class UpdateTrainsForForumCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly TrainTableYearRepository $train_table_year_repository,
        private readonly TrainTableRepository $train_table_repository,
    ) {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var TrainTableYear $train_table_year */
        $train_table_year = $this->train_table_year_repository->findTrainTableYearByDate(new \DateTime());

        // First we remove all current trainsForForum entries
        $trains_for_forum_repository = $this->doctrine->getManager()->getRepository(TrainsForForum::class);
        $current_trains = $trains_for_forum_repository->findAll();
        foreach ($current_trains as $train) {
            $this->doctrine->getManager()->remove($train);
        }
        $this->doctrine->getManager()->flush();

        $trains = $this->train_table_repository->findAllTrainTablesForForum($train_table_year);
        foreach ($trains as $train_data) {
            $train_for_forum = new TrainsForForum();
            $train_for_forum->train_number = $train_data[TrainTableRepository::FIELD_ROUTE_NUMBER];
            $train_for_forum->transporter_name = $train_data[TrainTableRepository::FIELD_TRANSPORTER_NAME];
            $train_for_forum->characteristic_name = $train_data[TrainTableRepository::FIELD_CHARACTERISTIC_NAME];
            $train_for_forum->characteristic_description = $train_data[TrainTableRepository::FIELD_CHARACTERISTIC_DESCRIPTION];
            $train_for_forum->first_location_name = $train_data['first_location'];
            $train_for_forum->first_location_time = (int) $train_data['first_time'];
            $train_for_forum->last_location_name = $train_data['last_location'];
            $train_for_forum->last_location_time = (int) $train_data['last_time'];
            $train_for_forum->section = $train_data[TrainTableRepository::FIELD_SECTION];

            $this->doctrine->getManager()->persist($train_for_forum);
        }
        $this->doctrine->getManager()->flush();

        return 0;
    }
}
