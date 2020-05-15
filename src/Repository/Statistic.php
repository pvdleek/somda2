<?php

namespace App\Repository;

use App\Entity\Statistic as StatisticEntity;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;

class Statistic extends EntityRepository
{
    public const BUSIEST_TYPE_PAGE_VIEWS = 0;
    public const BUSIEST_TYPE_SPOTS = 1;
    public const BUSIEST_TYPE_POSTS = 2;

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

    /**
     * @return array
     */
    public function getTotalsPerMonth(): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('YEAR(s.timestamp) AS year')
            ->addSelect('MONTH(s.timestamp) AS month')
            ->addSelect('SUM(s.visitorsHome) AS visitorsHome')
            ->addSelect('SUM(s.visitorsFunctions) AS visitorsFunctions')
            ->addSelect('SUM(s.visitorsTotal) AS visitorsTotal')
            ->addSelect('SUM(s.visitorsUnique) AS visitorsUnique')
            ->addSelect('SUM(s.numberOfSpots) AS numberOfSpots')
            ->addSelect('SUM(s.numberOfPosts) AS numberOfPosts')
            ->from(StatisticEntity::class, 's')
            ->addGroupBy('year')
            ->addGroupBy('month')
            ->orderBy('s.timestamp', 'DESC');
        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    public function getFirstDate(): DateTime
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s.timestamp')
            ->from(StatisticEntity::class, 's')
            ->orderBy('s.timestamp', 'ASC')
            ->setMaxResults(1);
        try {
            return new DateTime($queryBuilder->getQuery()->getSingleScalarResult());
        } catch (NonUniqueResultException $exception) {
            return new DateTime();
        } catch (NoResultException $exception) {
            return new DateTime();
        }
    }

    /**
     * @param int $type
     * @return array
     */
    public function findBusiest(int $type): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s.timestamp AS timestamp')
            ->addSelect('s.' . $this->getBusiestFieldName($type) . ' AS number')
            ->from(StatisticEntity::class, 's')
            ->orderBy('s.' . $this->getBusiestFieldName($type), 'DESC')
            ->setMaxResults(1);
        return $queryBuilder->getQuery()->getArrayResult()[0];
    }

    /**
     * @param int $type
     * @return string
     */
    private function getBusiestFieldName(int $type): string
    {
        if ($type === self::BUSIEST_TYPE_PAGE_VIEWS) {
            return 'visitorsTotal';
        }
        if ($type === self::BUSIEST_TYPE_SPOTS) {
            return 'numberOfSpots';
        }
        return 'numberOfPosts';
    }
}
