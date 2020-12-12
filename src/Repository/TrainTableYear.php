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
