<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Position as PositionEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PositionEntity::class);
    }

    /**
     * @return array
     */
    public function getAllAsArray(): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('p.id')
            ->addSelect('p.name')
            ->from(PositionEntity::class, 'p');
        return \array_column($queryBuilder->getQuery()->getResult(), 'name', 'id');
    }
}
