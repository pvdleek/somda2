<?php

namespace App\Repository;

use App\Entity\RouteOperationDays as RouteOperationDaysEntity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class RouteOperationDays extends EntityRepository
{
    /**
     * @param array $days
     * @return RouteOperationDaysEntity|null
     */
    public function findByDaysArray(array $days): ?RouteOperationDaysEntity
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r')
            ->from(RouteOperationDaysEntity::class, 'r');
        foreach ($days as $day => $value) {
            $queryBuilder->andWhere('r.' . $day . ' = ' . ($value ? 'TRUE' : 'FALSE'));
        }

        try {
            return $queryBuilder->getQuery()->getSingleResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            return null;
        }
    }
}
