<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TrainTableYear as TrainTableYearEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class TrainTableYearRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainTableYearEntity::class);
    }

    /**
     * @throws \Exception
     */
    public function findTrainTableYearByDate(\DateTime $check_date): ?TrainTableYearEntity
    {
        $check_date->setTime(0, 0);
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('t')
            ->from(TrainTableYearEntity::class, 't')
            ->andWhere(':checkDate BETWEEN t.start_date AND t.end_date')
            ->setParameter('checkDate', $check_date)
            ->setMaxResults(1);
        try {
            return $queryBuilder->getQuery()->getSingleResult();
        } catch (NoResultException) {
            return new TrainTableYearEntity();
        } catch (NonUniqueResultException) {
            $queryBuilder = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('t')
                ->from(TrainTableYearEntity::class, 't')
                ->setMaxResults(1);
            return $queryBuilder->getQuery()->getSingleResult();
        }
    }
}
