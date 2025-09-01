<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SpecialRoute as SpecialRouteEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SpecialRouteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpecialRouteEntity::class);
    }

    /**
     * @return SpecialRouteEntity[]
     * @throws \Exception
     */
    public function findForDashboard(): array
    {
        $today = new \DateTime();
        $today->setTime(0, 0);
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(SpecialRouteEntity::class, 's')
            ->andWhere('s.public = TRUE')
            ->andWhere('(s.start_date >= :today AND s.end_date IS NULL) OR s.end_date >= :today')
            ->setParameter('today', $today)
            ->addOrderBy('s.start_date', 'ASC');
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
            ->andWhere('(s.start_date >= :today AND s.end_date IS NULL) OR s.end_date >= :today')
            ->setParameter('today', new \DateTime())
            ->addOrderBy('s.start_date', 'ASC')
            ->setMaxResults($limit);
        return $queryBuilder->getQuery()->getResult();
    }
}
