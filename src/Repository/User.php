<?php

namespace App\Repository;

use App\Entity\User as UserEntity;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;

class User extends EntityRepository
{
    /**
     * @return array
     */
    public function findActiveForStaticData(): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u.username')
            ->addSelect('u.name')
            ->from(UserEntity::class, 'u')
            ->andWhere('u.active = TRUE');
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return int
     */
    public function countActive(): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(UserEntity::class, 'u')
            ->andWhere('u.active = TRUE');
        try {
            return (int)$queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $exception) {
            return 0;
        } catch (NoResultException $exception) {
            return 0;
        }
    }

    /**
     * @return int
     * @throws Exception
     */
    public function countBirthdays(): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(UserEntity::class, 'u')
            ->andWhere('u.active = TRUE')
            ->join('u.info', 'i')
            ->andWhere('i.birthDate = :today')
            ->setParameter('today', new DateTime());
        try {
            return (int)$queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $exception) {
            return 0;
        } catch (NoResultException $exception) {
            return 0;
        }
    }

    /**
     * @return UserEntity[]
     */
    public function findBanned(): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->from(UserEntity::class, 'u')
            ->andWhere('u.banExpireTimestamp IS NOT NULL');
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return UserEntity[]
     */
    public function findNonActivated(): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->from(UserEntity::class, 'u')
            ->andWhere('u.active = FALSE')
            ->andWhere('u.activationKey IS NOT NULL')
            ->andWhere('u.registerTimestamp >= :minimumDate')
            ->andWhere('u.registerTimestamp < :maximumDate')
            ->setParameter('minimumDate', new DateTime('2016-01-01'))
            ->setParameter('maximumDate', new DateTime('-5 days'));
        return $queryBuilder->getQuery()->getResult();
    }
}
