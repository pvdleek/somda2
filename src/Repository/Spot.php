<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Route;
use App\Entity\RouteOperationDays;
use App\Entity\Spot as SpotEntity;
use App\Entity\TrainTableYear;
use App\Entity\User;
use App\Generics\DateGenerics;
use App\Model\DataTableOrder;
use App\Model\Spot as SpotModel;
use App\Model\SpotFilter;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Exception;

class Spot extends EntityRepository
{
    private const FIELD_SPOT_DATE = 'spotDate';
    private const FIELD_LOCATION = 'location';

    /**
     * @var string[]
     */
    private static array $orderColumn = [
        self::FIELD_SPOT_DATE => 's.spotDate',
        self::FIELD_LOCATION => 'l.name',
        'train' => 't.number',
        'route' => 'r.number',
        'extra' => 'e.extra',
        'userExtra' => 'e.userExtra',
    ];

    /**
     * @param TrainTableYear|null $trainTableYear
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(?TrainTableYear $trainTableYear = null): QueryBuilder
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s.id AS id')
            ->addSelect('s.spotDate AS spotDate')
            ->addSelect('r.number AS routeNumber')
            ->addSelect('p.name AS positionName')
            ->addSelect('t.number AS trainNumber')
            ->addSelect('np.name AS namePatternName')
            ->addSelect('e.extra AS extra')
            ->addSelect('u.id AS spotterId')
            ->addSelect('u.username AS spotterUsername')
            ->addSelect('l.name AS locationName')
            ->addSelect('l.description AS locationDescription')

            ->from(SpotEntity::class, 's')
            ->join('s.route', 'r')
            ->join('s.position', 'p')
            ->join('s.train', 't')
            ->join('s.user', 'u')
            ->join('s.location', 'l')
            ->leftJoin('t.namePattern', 'np')
            ->leftJoin('s.extra', 'e')
            ->addOrderBy('s.timestamp', 'DESC');

        if (!is_null($trainTableYear)) {
            $queryBuilder
                ->addSelect('tt.time AS spotTime')
                ->join(
                    RouteOperationDays::class,
                    'rd',
                    Join::WITH,
                    'BIT_AND(rd.id, POWER(2, s.dayNumber)) = POWER(2, s.dayNumber)'
                )
                ->leftJoin(
                    'r.trainTables',
                    'tt',
                    Join::WITH,
                    'tt.trainTableYear = :trainTableYear AND tt.location = s.location AND rd.id = tt.routeOperationDays'
                )
                ->setParameter('trainTableYear', $trainTableYear)
                ->addGroupBy('s.id');
        }

        return $queryBuilder;
    }

    /**
     * @param array $idArray
     * @param User $user
     * @return SpotModel[]
     */
    public function findByIdsAndUserForDisplay(array $idArray, User $user): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->andWhere('s.id IN (:idArray)')
            ->setParameter('idArray', $idArray)
            ->andWhere('s.user = :user')
            ->setParameter('user', $user);
        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @param array $idArray
     * @param User $user
     * @return SpotEntity[]
     */
    public function findByIdsAndUser(array $idArray, User $user): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(SpotEntity::class, 's')
            ->andWhere('s.id IN (:idArray)')
            ->setParameter('idArray', $idArray)
            ->andWhere('s.user = :user')
            ->setParameter('user', $user);
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param User|null $user
     * @return int
     */
    public function countAll(?User $user = null): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(s.id)')
            ->from(SpotEntity::class, 's');
        if (!is_null($user)) {
            $queryBuilder->andWhere('s.user = :user')->setParameter('user', $user);
        }
        try {
            return (int)$queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException $exception) {
            return 0;
        }
    }

    /**
     * @param int $maxMonths
     * @param SpotFilter $spotFilter
     * @return SpotEntity[]
     * @throws Exception
     */
    public function findWithSpotFilter(int $maxMonths, SpotFilter $spotFilter): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(SpotEntity::class, 's')
            ->join('s.train', 't')
            ->join('s.route', 'r')
            ->join('s.location', 'l')
            ->addOrderBy('s.timestamp', 'DESC');
        $this->applySpotFilter($queryBuilder, $spotFilter, $maxMonths);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param int $maxMonths
     * @param SpotFilter $spotFilter
     * @param TrainTableYear $trainTableYear
     * @return SpotModel[]
     * @throws Exception
     */
    public function findRecentWithSpotFilter(
        int $maxMonths,
        SpotFilter $spotFilter,
        TrainTableYear $trainTableYear
    ): array {
        $queryBuilder = $this->getBaseQueryBuilder($trainTableYear);
        $this->applySpotFilter($queryBuilder, $spotFilter, $maxMonths);

        $queryResults = $queryBuilder->getQuery()->getArrayResult();

        $results = [];
        foreach ($queryResults as $queryResult) {
            $results[] = new SpotModel($queryResult);
        }
        return $results;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param SpotFilter $spotFilter
     * @param int $maxMonths
     * @throws Exception
     */
    private function applySpotFilter(QueryBuilder $queryBuilder, SpotFilter $spotFilter, int $maxMonths): void
    {
        if (!is_null($spotFilter->location)) {
            $queryBuilder
                ->andWhere('l.name = :location')
                ->setParameter(self::FIELD_LOCATION, $spotFilter->location);
        }
        if ($spotFilter->dayNumber > 0) {
            $queryBuilder
                ->andWhere('DAYOFWEEK(s.spotDate) = :dayNumber')
                ->setParameter('dayNumber', $spotFilter->dayNumber);
        }
        if (is_null($spotFilter->spotDate)) {
            $queryBuilder
                ->andWhere('s.timestamp > :minDate')
                ->setParameter('minDate', new DateTime('-' . $maxMonths . ' months'));
        } else {
            $queryBuilder
                ->andWhere('s.spotDate = :' . self::FIELD_SPOT_DATE)
                ->setParameter(
                    self::FIELD_SPOT_DATE,
                    $spotFilter->spotDate->format(DateGenerics::DATE_FORMAT_DATABASE)
                );
        }
        $this->filterOnTrainNumber($queryBuilder, true, $spotFilter->trainNumber);
        $this->filterOnRouteNumber($queryBuilder, true, $spotFilter->routeNumber);
    }

    /**
     * @param User $user
     * @param SpotFilter $spotFilter
     * @param int $numberOfRecords
     * @param int $offset
     * @param DataTableOrder[] $orderArray
     * @return SpotEntity[]
     */
    public function findForMySpots(
        User $user,
        SpotFilter $spotFilter,
        int $numberOfRecords,
        int $offset,
        array $orderArray
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
            ->setMaxResults($numberOfRecords)
            ->setFirstResult($offset);

        $this->filterOnSpotDate($queryBuilder, $spotFilter->spotDate);
        $this->filterOnLocation($queryBuilder, $spotFilter->location);
        $this->filterOnTrainNumber($queryBuilder, false, $spotFilter->trainNumber);
        $this->filterOnRouteNumber($queryBuilder, false, $spotFilter->routeNumber);

        if (count($orderArray) > 0) {
            foreach ($orderArray as $order) {
                $queryBuilder->addOrderBy(self::$orderColumn[$order->column], $order->ascending ? 'ASC' : 'DESC');
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param User $user
     * @param SpotFilter $spotFilter
     * @return int
     */
    public function countForMySpots(User $user, SpotFilter $spotFilter): int
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

        $this->filterOnSpotDate($queryBuilder, $spotFilter->spotDate);
        $this->filterOnLocation($queryBuilder, $spotFilter->location);
        $this->filterOnTrainNumber($queryBuilder, false, $spotFilter->trainNumber);
        $this->filterOnRouteNumber($queryBuilder, false, $spotFilter->routeNumber);

        try {
            return (int)$queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException $exception) {
            return 0;
        }
    }

    /**
     * @param DateTime $checkDate
     * @return array
     */
    public function findForRouteTrains(DateTime $checkDate): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->addSelect('r.id AS routeId')
            ->addSelect('n.id AS patternId')
            ->addSelect('p.id AS positionId')
            ->addSelect('DAYOFWEEK(s.timestamp) AS dayOfWeek')
            ->addSelect('COUNT(s.id) AS numberOfSPots')
            ->from(Route::class, 'r')
            ->join('r.spots', 's', Join::WITH, 's.timestamp >= :checkDate')
            ->join('s.train', 't')
            ->join('t.namePattern', 'n')
            ->join('s.position', 'p')
            ->setParameter('checkDate', $checkDate)
            ->addGroupBy('r.id')
            ->addGroupBy('n.id')
            ->addGroupBy('dayOfWeek');
        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param DateTime|null $spotDate
     */
    private function filterOnSpotDate(QueryBuilder $queryBuilder, ?DateTime $spotDate = null): void
    {
        if (!is_null($spotDate)) {
            $queryBuilder
                ->andWhere('DATE(s.spotDate) = :' . self::FIELD_SPOT_DATE)
                ->setParameter(self::FIELD_SPOT_DATE, $spotDate->format(DateGenerics::DATE_FORMAT_DATABASE));
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string|null $location
     */
    private function filterOnLocation(QueryBuilder $queryBuilder, ?string $location = null): void
    {
        if (!is_null($location)) {
            $queryBuilder
                ->andWhere('l.name LIKE :location')
                ->setParameter(self::FIELD_LOCATION, '%' . $location . '%');
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $exact
     * @param string|null $trainNumber
     */
    private function filterOnTrainNumber(QueryBuilder $queryBuilder, bool $exact, ?string $trainNumber = null): void
    {
        if (!is_null($trainNumber)) {
            if ($exact) {
                if (strpos($trainNumber, '*') !== false) {
                    // The train-number contains a wildcard
                    $queryBuilder
                        ->andWhere('t.number LIKE :trainNumber')
                        ->setParameter('trainNumber', str_replace('*', '%', $trainNumber))
                        ->andWhere('LENGTH(t.number) = :trainNumberLength')
                        ->setParameter('trainNumberLength', strlen($trainNumber));
                } else {
                    $queryBuilder->andWhere('t.number = :trainNumber')->setParameter('trainNumber', $trainNumber);
                }
            } else {
                $queryBuilder
                    ->andWhere('t.number LIKE :trainNumber')
                    ->setParameter('trainNumber', '%' . $trainNumber . '%');
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param bool $exact
     * @param string|null $routeNumber
     */
    private function filterOnRouteNumber(QueryBuilder $queryBuilder, bool $exact, ?string $routeNumber = null): void
    {
        if (!is_null($routeNumber)) {
            if ($exact) {
                if (strpos($routeNumber, '*') !== false) {
                    // The route-number contains a wildcard
                    $queryBuilder
                        ->andWhere('r.number LIKE :routeNumber')
                        ->setParameter('routeNumber', str_replace('*', '%', $routeNumber))
                        ->andWhere('LENGTH(r.number) = :routeNumberLength')
                        ->setParameter('routeNumberLength', strlen($routeNumber));
                } else {
                    $queryBuilder->andWhere('r.number = :routeNumber')->setParameter('routeNumber', $routeNumber);
                }
            } else {
                $queryBuilder
                    ->andWhere('r.number LIKE :routeNumber')
                    ->setParameter('routeNumber', '%' . $routeNumber . '%');
            }
        }
    }
}
