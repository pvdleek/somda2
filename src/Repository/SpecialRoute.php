<?php

namespace App\Repository;

use App\Entity\SpecialRoute as SpecialRouteEntity;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Exception;

class SpecialRoute extends EntityRepository
{
    /**
     * @param bool $construction
     * @return SpecialRouteEntity[]
     * @throws Exception
     */
    public function findForDashboard(bool $construction): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(SpecialRouteEntity::class, 's')
            ->andWhere('s.public = TRUE')
            ->andWhere('s.construction = :construction')
            ->setParameter('construction', $construction)
            ->andWhere('(s.startDate >= :today AND s.endDate IS NULL) OR s.endDate >= :today')
            ->setParameter('today', new DateTime())
            ->addOrderBy('s.startDate', 'ASC');
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param int $limit
     * @return SpecialRouteEntity[]
     * @throws Exception
     */
    public function findForFeed(int $limit): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(SpecialRouteEntity::class, 's')
            ->andWhere('s.public = TRUE')
            ->andWhere('(s.startDate >= :today AND s.endDate IS NULL) OR s.endDate >= :today')
            ->setParameter('today', new DateTime())
            ->addOrderBy('s.startDate', 'ASC')
            ->setMaxResults($limit);
        return $queryBuilder->getQuery()->getResult();
    }
}
