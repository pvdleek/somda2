<?php

namespace App\Repository;

use App\Entity\Route;
use App\Entity\Spot as SpotEntity;
use App\Entity\User;
use App\Model\DataTableOrder;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Exception;

class Spot extends EntityRepository
{
    /**
     * @var string[]
     */
    private static array $orderColumn = [
        'timestamp' => 's.spotDate',
        'location' => 'l.name',
        'train' => 't.number',
        'route' => 'r.number',
        'extra' => 'e.extra',
        'userExtra' => 'e.userExtra',
    ];

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
        } catch (NonUniqueResultException $exception) {
            return 0;
        } catch (NoResultException $exception) {
            return 0;
        }
    }

    /**
     * @param int $maxYears
     * @param string|null $location
     * @param int|null $dayNumber
     * @param string|null $trainNumber
     * @param string|null $routeNumber
     * @return SpotEntity[]
     * @throws Exception
     */
    public function findWithFilters(
        ?int $maxYears,
        ?string $location = null,
        ?int $dayNumber = 0,
        ?string $trainNumber = null,
        ?string $routeNumber = null
    ): array {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(SpotEntity::class, 's')
            ->andWhere('s.timestamp > :minDate')
            ->setParameter('minDate', new DateTime('-' . $maxYears . ' years'))
            ->addOrderBy('s.timestamp', 'DESC');

        if (!is_null($location)) {
            $queryBuilder->join('s.location', 'l')->andWhere('l.name = :location')->setParameter('location', $location);
        }
        if ($dayNumber > 0) {
            $queryBuilder->andWhere('DAYOFWEEK(s.timestamp) = :dayNumber')->setParameter('dayNumber', $dayNumber);
        }
        if (!is_null($trainNumber)) {
            $queryBuilder
                ->join('s.train', 't')
                ->andWhere('t.number = :trainNumber')
                ->setParameter('trainNumber', $trainNumber);
        }
        if (!is_null($routeNumber)) {
            $queryBuilder
                ->join('s.route', 'r')
                ->andWhere('r.number = :routeNumber')
                ->setParameter('routeNumber', $routeNumber);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param User $user
     * @param string|null $location
     * @param string|null $trainNumber
     * @param string|null $routeNumber
     * @param int $numberOfRecords
     * @param int $offset
     * @param DataTableOrder[] $orderArray
     * @return SpotEntity[]
     */
    public function findForMySpots(
        User $user,
        ?string $location,
        ?string $trainNumber,
        ?string $routeNumber,
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

        if (!is_null($location)) {
            $queryBuilder->andWhere('l.name LIKE :location')->setParameter('location', '%' . $location . '%');
        }
        if (!is_null($trainNumber)) {
            $queryBuilder
                ->andWhere('t.number LIKE :trainNumber')
                ->setParameter('trainNumber', '%' . $trainNumber . '%');
        }
        if (!is_null($routeNumber)) {
            $queryBuilder
                ->andWhere('r.number LIKE :routeNumber')
                ->setParameter('routeNumber', '%' . $routeNumber . '%');
        }

        if (count($orderArray) > 0) {
            foreach ($orderArray as $order) {
                $queryBuilder->addOrderBy(self::$orderColumn[$order->column], $order->ascending ? 'ASC' : 'DESC');
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param User $user
     * @param string|null $location
     * @param string|null $trainNumber
     * @param string|null $routeNumber
     * @return int
     */
    public function countForMySpots(User $user, ?string $location, ?string $trainNumber, ?string $routeNumber): int
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

        if (!is_null($location)) {
            $queryBuilder->andWhere('l.name LIKE :location')->setParameter('location', '%' . $location . '%');
        }
        if (!is_null($trainNumber)) {
            $queryBuilder
                ->andWhere('t.number LIKE :trainNumber')
                ->setParameter('trainNumber', '%' . $trainNumber . '%');
        }
        if (!is_null($routeNumber)) {
            $queryBuilder
                ->andWhere('r.number LIKE :routeNumber')
                ->setParameter('routeNumber', '%' . $routeNumber . '%');
        }

        try {
            return (int)$queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $exception) {
            return 0;
        } catch (NoResultException $exception) {
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
}
