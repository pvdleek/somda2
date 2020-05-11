<?php

namespace App\Repository;

use App\Entity\TrainTableYear as TrainTableYearEntity;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;

class TrainTableYear extends EntityRepository
{
    /**
     * @param DateTime $checkDate
     * @return TrainTableYearEntity|null
     * @throws Exception
     */
    public function findTrainTableYearByDate(DateTime $checkDate): ?TrainTableYearEntity
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('t')
            ->from(TrainTableYearEntity::class, 't')
            ->andWhere('t.startDate <= :checkDate')
            ->andWhere('t.endDate > :checkDate')
            ->setParameter('checkDate', $checkDate)
            ->setMaxResults(1);
        try {
            return $queryBuilder->getQuery()->getSingleResult();
        } catch (NoResultException $exception) {
            return new TrainTableYearEntity();
        } catch (NonUniqueResultException $exception) {
            $queryBuilder = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('t')
                ->from(TrainTableYearEntity::class, 't')
                ->setMaxResults(1);
            return $queryBuilder->getQuery()->getSingleResult();
        }
    }
}
