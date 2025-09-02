<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Route;
use App\Entity\Spot as SpotEntity;
use App\Entity\TrainTableYear;
use App\Entity\User;
use App\Generics\DateGenerics;
use App\Model\DataTableOrder;
use App\Model\Spot as SpotModel;
use App\Model\SpotFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class SpotRepository extends ServiceEntityRepository
{
    private const FIELD_SPOT_DATE = 'spot_date';
    private const FIELD_LOCATION = 'location';

    private static array $orderColumn = [
        self::FIELD_SPOT_DATE => 's.spot_date',
        self::FIELD_LOCATION => 'l.name',
        'train' => 't.number',
        'route' => 'r.number',
        'extra' => 'e.extra',
        'user_extra' => 'e.user_extra',
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpotEntity::class);
    }

    private function getBaseQueryBuilder(?TrainTableYear $train_table_year = null): QueryBuilder
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s.id AS id')
            ->addSelect('s.spot_date AS spot_date')
            ->addSelect('r.number AS route_number')
            ->addSelect('p.name AS position_name')
            ->addSelect('t.number AS train_number')
            ->addSelect('np.name AS name_pattern_name')
            ->addSelect('e.extra AS extra')
            ->addSelect('u.id AS spotter_id')
            ->addSelect('u.username AS spotter_username')
            ->addSelect('l.name AS location_name')
            ->addSelect('l.description AS location_description')

            ->from(SpotEntity::class, 's')
            ->join('s.route', 'r')
            ->join('s.position', 'p')
            ->join('s.train', 't')
            ->join('s.user', 'u')
            ->join('s.location', 'l')
            ->leftJoin('t.name_pattern', 'np')
            ->leftJoin('s.extra', 'e')
            ->addOrderBy('s.timestamp', 'DESC');

        if (null !== $train_table_year) {
            $queryBuilder
                ->addSelect('tt.time AS spot_time')
                ->leftJoin(
                    'r.train_tables',
                    'tt',
                    Join::WITH,
                    'tt.train_table_year = :train_table_year AND tt.route = s.route AND tt.location = s.location'
                )
                ->leftJoin(
                    'tt.routeOperationDays',
                    'rd',
                    Join::WITH,
                    'BIT_AND(rd.id, POWER(2, s.day_number)) = POWER(2, s.day_number)'
                )
                ->setParameter('train_table_year', $train_table_year)
                ->addGroupBy('s.id');
        }

        return $queryBuilder;
    }

    /**
     * @return SpotModel[]
     */
    public function findByIdsAndUserForDisplay(array $id_array, User $user): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->andWhere('s.id IN (:idArray)')
            ->setParameter('idArray', $id_array)
            ->andWhere('s.user = :user')
            ->setParameter('user', $user);
        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @return SpotEntity[]
     */
    public function findByIdsAndUser(array $id_array, User $user): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(SpotEntity::class, 's')
            ->andWhere('s.id IN (:idArray)')
            ->setParameter('idArray', $id_array)
            ->andWhere('s.user = :user')
            ->setParameter('user', $user);
        return $queryBuilder->getQuery()->getResult();
    }

    public function countAll(?User $user = null): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(s.id)')
            ->from(SpotEntity::class, 's');
        if (null !== $user) {
            $queryBuilder->andWhere('s.user = :user')->setParameter('user', $user);
        }
        try {
            return (int) $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException) {
            return 0;
        }
    }

    /**
     * @return SpotEntity[]
     * @throws \Exception
     */
    public function findWithSpotFilter(int $max_months, SpotFilter $spot_filter): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(SpotEntity::class, 's')
            ->join('s.train', 't')
            ->join('s.route', 'r')
            ->join('s.location', 'l')
            ->addOrderBy('s.timestamp', 'DESC');
        $this->applySpotFilter($queryBuilder, $spot_filter, $max_months);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return SpotModel[]
     * @throws \Exception
     */
    public function findRecentWithSpotFilter(
        int $max_months,
        SpotFilter $spot_filter,
        TrainTableYear $train_table_year
    ): array {
        $queryBuilder = $this->getBaseQueryBuilder($train_table_year);
        $this->applySpotFilter($queryBuilder, $spot_filter, $max_months);

        $query_results = $queryBuilder->getQuery()->getArrayResult();

        $results = [];
        foreach ($query_results as $query_result) {
            $results[] = new SpotModel($query_result);
        }
        return $results;
    }

    /**
     * @throws \Exception
     */
    private function applySpotFilter(QueryBuilder $query_builder, SpotFilter $spot_filter, int $max_months): void
    {
        if (null !== $spot_filter->location) {
            $query_builder
                ->andWhere('l.name = :location')
                ->setParameter(self::FIELD_LOCATION, $spot_filter->location);
        }
        if ($spot_filter->day_number > 0) {
            $query_builder
                ->andWhere('DAYOFWEEK(s.spot_date) = :day_number')
                ->setParameter('day_number', $spot_filter->day_number);
        }
        if (null === $spot_filter->spot_date) {
            $query_builder
                ->andWhere('s.timestamp > :minDate')
                ->setParameter('minDate', new \DateTime('-' . $max_months . ' months'));
        } else {
            $query_builder
                ->andWhere('s.spot_date = :' . self::FIELD_SPOT_DATE)
                ->setParameter(
                    self::FIELD_SPOT_DATE,
                    $spot_filter->spot_date->format(DateGenerics::DATE_FORMAT_DATABASE)
                );
        }
        $this->filterOnTrainNumber($query_builder, true, $spot_filter->train_number);
        $this->filterOnRouteNumber($query_builder, true, $spot_filter->route_number);
    }

    /**
     * @param DataTableOrder[] $order_array
     * @return SpotEntity[]
     */
    public function findForMySpots(
        User $user,
        SpotFilter $spot_filter,
        int $number_of_records,
        int $offset,
        array $order_array
    ): array {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(SpotEntity::class, 's')
            ->join('s.location', 'l')
            ->join('s.train', 't')
            ->join('s.route', 'r')
            ->leftJoin('s.extra', 'e')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)
            ->setMaxResults($number_of_records)
            ->setFirstResult($offset);

        $this->filterOnSpotDate($queryBuilder, $spot_filter->spot_date);
        $this->filterOnLocation($queryBuilder, $spot_filter->location);
        $this->filterOnTrainNumber($queryBuilder, false, $spot_filter->train_number);
        $this->filterOnRouteNumber($queryBuilder, false, $spot_filter->route_number);

        if (\count($order_array) > 0) {
            foreach ($order_array as $order) {
                $queryBuilder->addOrderBy(self::$orderColumn[$order->column], $order->ascending ? 'ASC' : 'DESC');
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function countForMySpots(User $user, SpotFilter $spot_filter): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(s.id)')
            ->from(SpotEntity::class, 's')
            ->join('s.location', 'l')
            ->join('s.train', 't')
            ->join('s.route', 'r')
            ->leftJoin('s.extra', 'e')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user);

        $this->filterOnSpotDate($queryBuilder, $spot_filter->spot_date);
        $this->filterOnLocation($queryBuilder, $spot_filter->location);
        $this->filterOnTrainNumber($queryBuilder, false, $spot_filter->train_number);
        $this->filterOnRouteNumber($queryBuilder, false, $spot_filter->route_number);

        try {
            return (int) $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException) {
            return 0;
        }
    }

    public function findForRouteTrains(\DateTime $check_date): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->addSelect('r.id AS route_id')
            ->addSelect('n.id AS pattern_id')
            ->addSelect('p.id AS position_id')
            ->addSelect('DAYOFWEEK(s.timestamp) AS day_of_week')
            ->addSelect('COUNT(s.id) AS number_of_spots')
            ->from(Route::class, 'r')
            ->join('r.spots', 's', Join::WITH, 's.timestamp >= :check_date')
            ->join('s.train', 't')
            ->join('t.name_pattern', 'n')
            ->join('s.position', 'p')
            ->setParameter('check_date', $check_date)
            ->addGroupBy('r.id')
            ->addGroupBy('n.id')
            ->addGroupBy('day_of_week');
        return $queryBuilder->getQuery()->getArrayResult();
    }

    private function filterOnSpotDate(QueryBuilder $query_builder, ?\DateTime $spot_date = null): void
    {
        if (null !== $spot_date) {
            $query_builder
                ->andWhere('DATE(s.spot_date) = :' . self::FIELD_SPOT_DATE)
                ->setParameter(self::FIELD_SPOT_DATE, $spot_date->format(DateGenerics::DATE_FORMAT_DATABASE));
        }
    }

    private function filterOnLocation(QueryBuilder $query_builder, ?string $location = null): void
    {
        if (null !== $location) {
            $query_builder
                ->andWhere('l.name LIKE :location')
                ->setParameter(self::FIELD_LOCATION, '%' . $location . '%');
        }
    }

    private function filterOnTrainNumber(QueryBuilder $query_builder, bool $exact, ?string $train_number = null): void
    {
        if (null !== $train_number) {
            if ($exact) {
                if (\strpos($train_number, '*') !== false) {
                    // The train-number contains a wildcard
                    $query_builder
                        ->andWhere('t.number LIKE :train_number')
                        ->setParameter('train_number', str_replace('*', '%', $train_number))
                        ->andWhere('LENGTH(t.number) = :train_number_length')
                        ->setParameter('train_number_length', strlen($train_number));
                } else {
                    $query_builder->andWhere('t.number = :train_number')->setParameter('train_number', $train_number);
                }
            } else {
                $query_builder
                    ->andWhere('t.number LIKE :train_number')
                    ->setParameter('train_number', '%' . $train_number . '%');
            }
        }
    }

    private function filterOnRouteNumber(QueryBuilder $query_builder, bool $exact, ?string $route_number = null): void
    {
        if (null !== $route_number) {
            if ($exact) {
                if (\strpos($route_number, '*') !== false) {
                    // The route-number contains a wildcard
                    $query_builder
                        ->andWhere('r.number LIKE :route_number')
                        ->setParameter('route_number', str_replace('*', '%', $route_number))
                        ->andWhere('LENGTH(r.number) = :route_number_length')
                        ->setParameter('route_number_length', strlen($route_number));
                } else {
                    $query_builder->andWhere('r.number = :route_number')->setParameter('route_number', $route_number);
                }
            } else {
                $query_builder
                    ->andWhere('r.number LIKE :route_number')
                    ->setParameter('route_number', '%' . $route_number . '%');
            }
        }
    }
}
