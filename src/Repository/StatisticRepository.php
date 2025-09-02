<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Statistic as StatisticEntity;
use App\Model\StatisticBusiest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class StatisticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatisticEntity::class);
    }

    public function countPageViews(): int
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('SUM(s.visitors_total)')
            ->from(StatisticEntity::class, 's');
        try {
            return (int) $query_builder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException) {
            return 0;
        }
    }

    public function countSpots(): int
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('SUM(s.number_of_spots)')
            ->from(StatisticEntity::class, 's');
        try {
            return (int) $query_builder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException) {
            return 0;
        }
    }

    /**
     * @return StatisticEntity[]
     * @throws \Exception
     */
    public function findLastDays(int $numberOfDays): array
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(StatisticEntity::class, 's')
            ->orderBy('s.timestamp', 'DESC')
            ->setMaxResults($numberOfDays);
        return $query_builder->getQuery()->getResult();
    }

    public function getTotalsPerMonth(): array
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('YEAR(s.timestamp) AS year')
            ->addSelect('MONTH(s.timestamp) AS month')
            ->addSelect('SUM(s.visitors_home) AS visitors_home')
            ->addSelect('SUM(s.visitors_functions) AS visitors_functions')
            ->addSelect('SUM(s.visitors_total) AS visitors_total')
            ->addSelect('SUM(s.visitors_unique) AS visitors_unique')
            ->addSelect('SUM(s.number_of_spots) AS number_of_spots')
            ->addSelect('SUM(s.number_of_posts) AS number_of_posts')
            ->from(StatisticEntity::class, 's')
            ->addGroupBy('year')
            ->addGroupBy('month')
            ->orderBy('s.timestamp', 'DESC');
        return $query_builder->getQuery()->getArrayResult();
    }

    /**
     * @throws \Exception
     */
    public function getFirstDate(): \DateTime
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s.timestamp')
            ->from(StatisticEntity::class, 's')
            ->orderBy('s.timestamp', 'ASC')
            ->setMaxResults(1);
        try {
            return new \DateTime($query_builder->getQuery()->getSingleScalarResult());
        } catch (NonUniqueResultException | NoResultException) {
            return new \DateTime();
        }
    }

    public function findBusiest(StatisticBusiest $statisticBusiest): void
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s.timestamp AS timestamp')
            ->addSelect('s.'.$this->getBusiestFieldName($statisticBusiest->type).' AS number')
            ->from(StatisticEntity::class, 's')
            ->orderBy('s.'.$this->getBusiestFieldName($statisticBusiest->type), 'DESC')
            ->setMaxResults(1);
        $result = $query_builder->getQuery()->getArrayResult()[0];
        $statisticBusiest->timestamp = $result['timestamp'];
        $statisticBusiest->number = (int) $result['number'];
    }

    private function getBusiestFieldName(int $type): string
    {
        if ($type === StatisticBusiest::TYPE_PAGE_VIEWS) {
            return 'visitors_total';
        }
        if ($type === StatisticBusiest::TYPE_SPOTS) {
            return 'number_of_spots';
        }
        return 'number_of_posts';
    }
}
