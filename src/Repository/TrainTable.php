<?php

namespace App\Repository;

use App\Entity\Location;
use App\Entity\TrainTable as TrainTableEntity;
use App\Entity\TrainTableYear;
use Doctrine\ORM\EntityRepository;

class TrainTable extends EntityRepository
{
    /**
     * @param TrainTableYear $trainTableYear
     * @param Location $location
     * @param int $dayNumber
     * @param string $dayName
     * @param int $startTime
     * @param int $endTime
     * @return array
     */
    public function findPassingRoutes(
        TrainTableYear $trainTableYear,
        Location $location,
        int $dayNumber,
        string $dayName,
        int $startTime,
        int $endTime
    ): array {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->addSelect('t.time AS time')
            ->addSelect('t.action AS action')
            ->addSelect('route.number as route_number')
            ->addSelect('fl_first.name AS fl_first_name')
            ->addSelect('fl_first.description AS fl_first_description')
            ->addSelect('fl_last.name AS fl_last_name')
            ->addSelect('fl_last.description AS fl_last_description')
            ->addSelect('transporter.name AS transporter_name')
            ->addSelect('characteristic.description AS characteristic_description')
            ->from(TrainTableEntity::class, 't')
            ->andWhere('t.trainTableYear = :trainTableYear')
            ->setParameter('trainTableYear', $trainTableYear)
            ->andWhere('t.location = :location')
            ->setParameter('location', $location)
            ->andWhere('t.time >= :startTime')
            ->setParameter('startTime', $startTime)
            ->andWhere('t.time <= :endTime')
            ->setParameter('endTime', $endTime)
            ->join('t.routeOperationDays', 'routeOperationDays')
            ->andWhere('routeOperationDays.' . $dayName . ' = TRUE')
            ->join('t.route', 'route')
            ->join('route.trainTableFirstLasts', 'trainTableFirstLasts')
            ->andWhere('trainTableFirstLasts.dayNumber = :dayNumber')
            ->setParameter('dayNumber', $dayNumber + 1)
            ->andWhere('trainTableFirstLasts.trainTableYear = :trainTableYear')
            ->join('trainTableFirstLasts.firstLocation', 'fl_first')
            ->join('trainTableFirstLasts.lastLocation', 'fl_last')
            ->join('route.routeLists', 'routeLists')
            ->andWhere('routeLists.trainTableYear = :trainTableYear')
            ->join('routeLists.transporter', 'transporter')
            ->join('routeLists.characteristic', 'characteristic')
            ->addOrderBy('t.time', 'ASC');
        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @param TrainTableYear $trainTableYear
     * @return array
     */
    public function findAllTrainTablesForForum(TrainTableYear $trainTableYear): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r.number AS routeNumber')
            ->addSelect('tr.name AS transporter')
            ->addSelect('c.name AS characteristicName')
            ->addSelect('c.description AS characteristicDescription')
            ->addSelect('l1.name AS firstLocation')
            ->addSelect('fl.firstTime AS firstTime')
            ->addSelect('l2.name AS lastLocation')
            ->addSelect('fl.lastTime AS lastTime')
            ->addSelect('rl.section AS section')
            ->from(TrainTableEntity::class, 't')
            ->join('t.route', 'r')
            ->join('r.trainTableFirstLasts', 'fl')
            ->join('fl.firstLocation', 'l1')
            ->join('fl.lastLocation', 'l2')
            ->join('r.routeLists', 'rl')
            ->join('rl.transporter', 'tr')
            ->join('rl.characteristic', 'c')
            ->andWhere('t.trainTableYear = :trainTableYear')
            ->andWhere('fl.trainTableYear = :trainTableYear')
            ->setParameter('trainTableYear', $trainTableYear)
            ->andWhere('fl.dayNumber = 1')
            ->addGroupBy('r.id');
        return $queryBuilder->getQuery()->getArrayResult();
    }
}
