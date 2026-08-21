<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RouteOperationDays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RouteOperationDays>
 */
class RouteOperationDaysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RouteOperationDays::class);
    }

    /**
     * @param array<string, bool> $days
     * @return RouteOperationDays|null
     */
    public function findByDaysArray(array $days): ?RouteOperationDays
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r')
            ->from(RouteOperationDays::class, 'r');
        foreach ($days as $day => $value) {
            $query_builder->andWhere('r.'.$day.' = '.($value ? 'TRUE' : 'FALSE'));
        }

        try {
            return $query_builder->getQuery()->getSingleResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            return null;
        }
    }
}
