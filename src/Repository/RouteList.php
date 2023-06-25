<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\RouteList as RouteListEntity;
use App\Entity\TrainTableYear;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class RouteList extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RouteListEntity::class);
    }

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
        } catch (NonUniqueResultException | NoResultException $exception) {
            return null;
        }
    }

    /**
     * @return RouteListEntity[]
     */
    public function findForOverview(TrainTableYear $trainTableYear): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r')
            ->from(RouteListEntity::class, 'r')
            ->andWhere('r.trainTableYear = :trainTableYear')
            ->setParameter('trainTableYear', $trainTableYear)
            ->join('r.routes', 'routes')
            ->addOrderBy('r.firstNumber', 'ASC');
        return $queryBuilder->getQuery()->getResult();
    }
}
