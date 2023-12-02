<?php

namespace App\Repository;

use App\Entity\SpecialRoute as SpecialRouteEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SpecialRoute extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpecialRouteEntity::class);
    }

    /**
     * @return SpecialRouteEntity[]
     * @throws \Exception
     */
    public function findForDashboard(bool $construction): array
    {
        $today = new \DateTime();
        $today->setTime(0, 0);
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(SpecialRouteEntity::class, 's')
            ->andWhere('s.public = TRUE')
            ->andWhere('s.construction = :construction')
            ->setParameter('construction', $construction)
            ->andWhere('(s.startDate >= :today AND s.endDate IS NULL) OR s.endDate >= :today')
            ->setParameter('today', $today)
            ->addOrderBy('s.startDate', 'ASC');
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return SpecialRouteEntity[]
     * @throws \Exception
     */
    public function findForFeed(int $limit): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(SpecialRouteEntity::class, 's')
            ->andWhere('s.public = TRUE')
            ->andWhere('(s.startDate >= :today AND s.endDate IS NULL) OR s.endDate >= :today')
            ->setParameter('today', new \DateTime())
            ->addOrderBy('s.startDate', 'ASC')
            ->setMaxResults($limit);
        return $queryBuilder->getQuery()->getResult();
    }
}
