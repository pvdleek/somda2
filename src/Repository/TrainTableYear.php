<?php

namespace App\Repository;

use App\Entity\TrainTableYear as TrainTableYearEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class TrainTableYear extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainTableYearEntity::class);
    }

    /**
     * @throws \Exception
     */
    public function findTrainTableYearByDate(\DateTime $checkDate): ?TrainTableYearEntity
    {
        $checkDate->setTime(0, 0);
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('t')
            ->from(TrainTableYearEntity::class, 't')
            ->andWhere(':checkDate BETWEEN t.startDate AND t.endDate')
            ->setParameter('checkDate', $checkDate)
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
