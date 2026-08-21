<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RouteList;
use App\Entity\TrainTableYear;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RouteList>
 */
class RouteListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RouteList::class);
    }

    public function findForRouteNumber(TrainTableYear $train_table_year, int $route_number): ?RouteList
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r')
            ->from(RouteList::class, 'r')
            ->andWhere('r.train_table_year = :train_table_year')
            ->setParameter('train_table_year', $train_table_year)
            ->andWhere(':route_number BETWEEN r.first_number AND r.last_number')
            ->setParameter('route_number', $route_number)
            ->setMaxResults(1);
        try {
            return $query_builder->getQuery()->getSingleResult();
        } catch (NonUniqueResultException | NoResultException) {
            return null;
        }
    }

    /**
     * @return RouteList[]
     */
    public function findForOverview(TrainTableYear $train_table_year): array
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r')
            ->from(RouteList::class, 'r')
            ->andWhere('r.train_table_year = :train_table_year')
            ->setParameter('train_table_year', $train_table_year)
            ->join('r.routes', 'routes')
            ->addOrderBy('r.first_number', 'ASC');
        return $query_builder->getQuery()->getResult();
    }
}
