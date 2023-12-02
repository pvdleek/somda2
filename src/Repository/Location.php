<?php

namespace App\Repository;

use App\Entity\Location as LocationEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class Location extends ServiceEntityRepository
{
    private const PARAMETER_SEARCH = 'search';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocationEntity::class);
    }

    public function findOneByName(string $search): ?LocationEntity
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('l')
            ->from(LocationEntity::class, 'l')
            ->andWhere('LOWER(l.name) = :' . self::PARAMETER_SEARCH)
            ->setParameter(self::PARAMETER_SEARCH, strtolower($search));
        try {
            return $queryBuilder->getQuery()->getSingleResult();
        } catch (NonUniqueResultException | NoResultException) {
            return null;
        }
    }

    /**
     * @return LocationEntity[]
     */
    public function findByName(string $search): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('l')
            ->from(LocationEntity::class, 'l')
            ->andWhere('LOWER(l.name) LIKE :' . self::PARAMETER_SEARCH)
            ->setParameter(self::PARAMETER_SEARCH, strtolower($search));
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return LocationEntity[]
     */
    public function findByDescription(string $search): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('l')
            ->from(LocationEntity::class, 'l')
            ->andWhere('LOWER(l.description) LIKE :' . self::PARAMETER_SEARCH)
            ->setParameter(self::PARAMETER_SEARCH, strtolower($search));
        return $queryBuilder->getQuery()->getResult();
    }
}
