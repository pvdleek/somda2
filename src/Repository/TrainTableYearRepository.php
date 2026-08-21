<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TrainTableYear;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TrainTableYear>
 */
class TrainTableYearRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainTableYear::class);
    }

    /**
     * @throws \Exception
     */
    public function findTrainTableYearByDate(\DateTime $check_date): ?TrainTableYear
    {
        $check_date->setTime(0, 0);
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('t')
            ->from(TrainTableYear::class, 't')
            ->andWhere(':checkDate BETWEEN t.start_date AND t.end_date')
            ->setParameter('checkDate', $check_date)
            ->setMaxResults(1);
        try {
            return $query_builder->getQuery()->getSingleResult();
        } catch (NoResultException) {
            return new TrainTableYear();
        } catch (NonUniqueResultException) {
            $query_builder = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('t')
                ->from(TrainTableYear::class, 't')
                ->setMaxResults(1);
            return $query_builder->getQuery()->getSingleResult();
        }
    }
}
