<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User as UserEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEntity::class);
    }

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

    public function countActive(): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(UserEntity::class, 'u')
            ->andWhere('u.active = TRUE');
        try {
            return (int) $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException) {
            return 0;
        }
    }

    /**
     * @throws \Exception
     */
    public function countBirthdays(): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(UserEntity::class, 'u')
            ->andWhere('u.active = TRUE')
            ->join('u.info', 'i')
            ->andWhere('i.birth_date = :today')
            ->setParameter('today', new \DateTime());
        try {
            return (int) $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException | NoResultException) {
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
            ->andWhere('u.ban_expire_timestamp IS NOT NULL');
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
            ->andWhere('u.activation_key IS NOT NULL')
            ->andWhere('u.register_timestamp >= :minimumDate')
            ->andWhere('u.register_timestamp < :maximumDate')
            ->setParameter('minimumDate', new \DateTime('2016-01-01'))
            ->setParameter('maximumDate', new \DateTime('-5 days'));
        return $queryBuilder->getQuery()->getResult();
    }
}
