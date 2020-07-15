<?php

namespace App\Repository;

use App\Entity\RouteList as RouteListEntity;
use App\Entity\TrainTableYear;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class RouteList extends EntityRepository
{
    /**
     * @param TrainTableYear $trainTableYear
     * @param int $routeNumber
     * @return RouteListEntity|null
     */
    public function findForRouteNumber(TrainTableYear $trainTableYear, int $routeNumber): ?RouteListEntity
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r')
            ->from(RouteListEntity::class, 'r')
            ->andWhere('r.trainTableYear = :trainTableYear')
            ->setParameter('trainTableYear', $trainTableYear)
            ->andWhere(':routeNumber BETWEEN r.firstNumber AND r.lastNumber')
            ->setParameter('routeNumber', $routeNumber)
            ->setMaxResults(1);
        try {
            return $queryBuilder->getQuery()->getSingleResult();
        } catch (NonUniqueResultException $exception) {
            return null;
        } catch (NoResultException $exception) {
            return null;
        }
    }
}
