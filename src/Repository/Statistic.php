<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Statistic as StatisticEntity;
use App\Model\StatisticBusiest;
use DateTime;
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
            return (int)$queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException $exception) {
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
        } catch (NonUniqueResultException | NoResultException $exception) {
            return new DateTime();
        }
    }

    /**
     * @param StatisticBusiest $statisticBusiest
     */
    public function findBusiest(StatisticBusiest $statisticBusiest): void
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s.timestamp AS timestamp')
            ->addSelect('s.' . $this->getBusiestFieldName($statisticBusiest->type) . ' AS number')
            ->from(StatisticEntity::class, 's')
            ->orderBy('s.' . $this->getBusiestFieldName($statisticBusiest->type), 'DESC')
            ->setMaxResults(1);
        $result = $queryBuilder->getQuery()->getArrayResult()[0];
        $statisticBusiest->timestamp = $result['timestamp'];
        $statisticBusiest->number = (int)$result['number'];
    }

    /**
     * @param int $type
     * @return string
     */
    private function getBusiestFieldName(int $type): string
    {
        if ($type === StatisticBusiest::TYPE_PAGE_VIEWS) {
            return 'visitorsTotal';
        }
        if ($type === StatisticBusiest::TYPE_SPOTS) {
            return 'numberOfSpots';
        }
        return 'numberOfPosts';
    }
}
