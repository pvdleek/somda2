<?php

namespace App\Repository;

use App\Entity\Route;
use App\Entity\Spot as SpotEntity;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Exception;

class Spot extends EntityRepository
{
    /**
     * @return int
     */
    public function countAll(): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(s.id)')
            ->from(SpotEntity::class, 's');
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
        int $maxYears,
        string $location = null,
        int $dayNumber = 0,
        string $trainNumber = null,
        string $routeNumber = null
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
