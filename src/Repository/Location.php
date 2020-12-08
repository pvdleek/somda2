<?php

namespace App\Repository;

use App\Entity\Location as LocationEntity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class Location extends EntityRepository
{
    private const PARAMETER_SEARCH = 'search';

    /**
     * @param string $search
     * @return LocationEntity|null
     */
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
        } catch (NonUniqueResultException | NoResultException $exception) {
            return null;
        }
    }

    /**
     * @param string $search
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
     * @param string $search
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
