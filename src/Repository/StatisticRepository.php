<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Statistic;
use App\Model\StatisticBusiest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Statistic>
 */
class StatisticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Statistic::class);
    }

    public function countPageViews(): int
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('SUM(s.visitors_total)')
            ->from(Statistic::class, 's');
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
            ->from(Statistic::class, 's');
        try {
            return (int) $query_builder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException) {
            return 0;
        }
    }

    /**
     * @return Statistic[]
     * @throws \Exception
     */
    public function findLastDays(int $number_of_days): array
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from(Statistic::class, 's')
            ->orderBy('s.timestamp', 'DESC')
            ->setMaxResults($number_of_days);

        return $query_builder->getQuery()->getResult();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
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
            ->from(Statistic::class, 's')
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
            ->from(Statistic::class, 's')
            ->orderBy('s.timestamp', 'ASC')
            ->setMaxResults(1);
        try {
            return new \DateTime($query_builder->getQuery()->getSingleScalarResult());
        } catch (NonUniqueResultException | NoResultException) {
            return new \DateTime();
        }
    }

    public function findBusiest(StatisticBusiest $statistic_busiest): void
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s.timestamp AS timestamp')
            ->addSelect('s.'.$this->getBusiestFieldName($statistic_busiest->type).' AS number')
            ->from(Statistic::class, 's')
            ->orderBy('s.'.$this->getBusiestFieldName($statistic_busiest->type), 'DESC')
            ->setMaxResults(1);
        $result = $query_builder->getQuery()->getArrayResult()[0];

        $statistic_busiest->timestamp = $result['timestamp'];
        $statistic_busiest->number = (int) $result['number'];
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
