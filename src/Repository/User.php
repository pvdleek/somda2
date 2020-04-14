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
            return $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $exception) {
            return null;
        } catch (NoResultException $exception) {
            return null;
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
            return $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $exception) {
            return null;
        } catch (NoResultException $exception) {
            return null;
        }
    }
}
