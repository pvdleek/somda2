<?php

namespace App\Repository;

use App\Entity\SpecialRoute as SpecialRouteEntity;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Exception;

class SpecialRoute extends EntityRepository
{
    /**
     * @return SpecialRouteEntity[]
     * @throws Exception
     */
    public function findForDashboard(): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(SpecialRouteEntity::class, 's')
            ->andWhere('s.public = TRUE')
            ->andWhere('s.construction = FALSE')
            ->andWhere('(s.startDate >= :today AND s.endDate IS NULL) OR s.endDate >= :today')
            ->setParameter('today', new DateTime())
            ->addOrderBy('s.startDate', 'ASC');
        return $queryBuilder->getQuery()->getResult();
    }
}
