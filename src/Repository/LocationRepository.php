<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Location as LocationEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class LocationRepository extends ServiceEntityRepository
{
    private const PARAMETER_SEARCH = 'search';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocationEntity::class);
    }

    public function findOneByName(string $search): ?LocationEntity
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('l')
            ->from(LocationEntity::class, 'l')
            ->andWhere('LOWER(l.name) = :'.self::PARAMETER_SEARCH)
            ->setParameter(self::PARAMETER_SEARCH, strtolower($search));
        try {
            return $query_builder->getQuery()->getSingleResult();
        } catch (NonUniqueResultException | NoResultException) {
            return null;
        }
    }

    /**
     * @return LocationEntity[]
     */
    public function findByName(string $search): array
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('l')
            ->from(LocationEntity::class, 'l')
            ->andWhere('LOWER(l.name) LIKE :'.self::PARAMETER_SEARCH)
            ->setParameter(self::PARAMETER_SEARCH, strtolower($search));
        return $query_builder->getQuery()->getResult();
    }

    /**
     * @return LocationEntity[]
     */
    public function findByDescription(string $search): array
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('l')
            ->from(LocationEntity::class, 'l')
            ->andWhere('LOWER(l.description) LIKE :'.self::PARAMETER_SEARCH)
            ->setParameter(self::PARAMETER_SEARCH, strtolower($search));
        return $query_builder->getQuery()->getResult();
    }
}
