<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Location;
use App\Entity\Route;
use App\Entity\TrainTable as TrainTableEntity;
use App\Entity\TrainTableFirstLast;
use App\Entity\TrainTableYear;
use App\Traits\DateTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

class TrainTableRepository extends ServiceEntityRepository
{
    use DateTrait;

    public const FIELD_ROUTE_NUMBER = 'route_number';
    public const FIELD_TRANSPORTER_NAME = 'transporter_name';
    public const FIELD_CHARACTERISTIC_NAME = 'characteristicName';
    public const FIELD_CHARACTERISTIC_DESCRIPTION = 'characteristicDescription';
    public const FIELD_SECTION = 'section';

    private const PARAMETER_TRAIN_TABLE_YEAR = 'train_table_year';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainTableEntity::class);
    }

    public function findPassingRoutes(
        TrainTableYear $train_table_year,
        Location $location,
        int $day_number,
        int $start_time,
        int $end_time
    ): array {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->addSelect('t.time AS time')
            ->addSelect('t.action AS action')
            ->addSelect('route.number as route_number')
            ->addSelect('fl_first.name AS fl_first_name')
            ->addSelect('fl_first.description AS fl_first_description')
            ->addSelect('train_table_first_lasts.first_time AS fl_first_time')
            ->addSelect('fl_last.name AS fl_last_name')
            ->addSelect('fl_last.description AS fl_last_description')
            ->addSelect('train_table_first_lasts.last_time AS fl_last_time')
            ->addSelect('transporter.name AS '.self::FIELD_TRANSPORTER_NAME)
            ->addSelect('characteristic.name AS '.self::FIELD_CHARACTERISTIC_NAME)
            ->addSelect('characteristic.description AS '.self::FIELD_CHARACTERISTIC_DESCRIPTION)
            ->from(TrainTableEntity::class, 't')
            ->andWhere('t.train_table_year = :'.self::PARAMETER_TRAIN_TABLE_YEAR)
            ->setParameter(self::PARAMETER_TRAIN_TABLE_YEAR, $train_table_year)
            ->andWhere('t.location = :location')
            ->setParameter('location', $location)
            ->andWhere('t.time >= :start_time')
            ->setParameter('start_time', $start_time)
            ->andWhere('t.time <= :end_time')
            ->setParameter('end_time', $end_time)
            ->join('t.routeOperationDays', 'routeOperationDays')
            ->andWhere('routeOperationDays.'.$this->getDayName($day_number - 1).' = TRUE')
            ->join('t.route', 'route')
            ->join('route.train_table_first_lasts', 'train_table_first_lasts')
            ->andWhere('train_table_first_lasts.day_number = :day_number')
            ->setParameter('day_number', $day_number)
            ->andWhere('train_table_first_lasts.train_table_year = :'.self::PARAMETER_TRAIN_TABLE_YEAR)
            ->join('train_table_first_lasts.first_location', 'fl_first')
            ->join('train_table_first_lasts.last_location', 'fl_last')
            ->join('route.route_lists', 'route_lists')
            ->andWhere('route_lists.train_table_year = :'.self::PARAMETER_TRAIN_TABLE_YEAR)
            ->join('route_lists.transporter', 'transporter')
            ->join('route_lists.characteristic', 'characteristic')
            ->addOrderBy('t.time', 'ASC');
        return $query_builder->getQuery()->getArrayResult();
    }

    public function findAllTrainTablesForForum(TrainTableYear $train_table_year): array
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r.number AS '.self::FIELD_ROUTE_NUMBER)
            ->addSelect('tr.name AS '.self::FIELD_TRANSPORTER_NAME)
            ->addSelect('c.name AS '.self::FIELD_CHARACTERISTIC_NAME)
            ->addSelect('c.description AS '.self::FIELD_CHARACTERISTIC_DESCRIPTION)
            ->addSelect('l1.name AS first_location')
            ->addSelect('fl.first_time AS first_time')
            ->addSelect('l2.name AS last_location')
            ->addSelect('fl.last_time AS last_time')
            ->addSelect('rl.section AS '.self::FIELD_SECTION)
            ->from(TrainTableFirstLast::class, 'fl')
            ->join('fl.route', 'r')
            ->join('fl.first_location', 'l1')
            ->join('fl.last_location', 'l2')
            ->join('r.route_lists', 'rl', Join::WITH, 'rl.train_table_year = :'.self::PARAMETER_TRAIN_TABLE_YEAR)
            ->join('rl.transporter', 'tr')
            ->join('rl.characteristic', 'c')
            ->andWhere('fl.train_table_year = :'.self::PARAMETER_TRAIN_TABLE_YEAR)
            ->addGroupBy('r.number')
            ->setParameter(self::PARAMETER_TRAIN_TABLE_YEAR, $train_table_year);
        return $query_builder->getQuery()->getArrayResult();
    }

    public function isExistingForSpot(
        TrainTableYear $train_table_year,
        Route $route,
        Location $location,
        int $day_number
    ): bool {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(t.id)')
            ->from(TrainTableEntity::class, 't')
            ->andWhere('t.train_table_year = :'.self::PARAMETER_TRAIN_TABLE_YEAR)
            ->setParameter(self::PARAMETER_TRAIN_TABLE_YEAR, $train_table_year)
            ->andWhere('t.route = :route')
            ->setParameter('route', $route)
            ->andWhere('t.location = :location')
            ->setParameter('location', $location)
            ->join('t.routeOperationDays', 'o')
            ->andWhere('o.'.$this->getDayName($day_number - 1) .' = TRUE');
        try {
            return (int) $query_builder->getQuery()->getSingleScalarResult() > 0;
        } catch (NonUniqueResultException | NoResultException) {
            return false;
        }
    }
}
