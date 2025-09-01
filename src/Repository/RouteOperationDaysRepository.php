<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RouteOperationDays as RouteOperationDaysEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class RouteOperationDaysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RouteOperationDaysEntity::class);
    }

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
