<?php

namespace App\Repository;

use App\Entity\Statistic as StatisticEntity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;

class Statistic extends EntityRepository
{
    /**
     * @return int
     */
    public function countPageViews(): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('SUM(s.visitorsTotal)')
            ->from(StatisticEntity::class, 's');
        try {
            return $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $exception) {
            return 0;
        } catch (NoResultException $exception) {
            return 0;
        }
    }

    /**
     * @param int $numberOfDays
     * @return StatisticEntity[]
     * @throws Exception
     */
    public function findLastDays(int $numberOfDays): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(StatisticEntity::class, 's')
            ->orderBy('s.timestamp', 'DESC')
            ->setMaxResults($numberOfDays);
        return $queryBuilder->getQuery()->getResult();
    }
}
