<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Position;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Position>
 */
class PositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Position::class);
    }

    /**
     * @return array<int, string>
     */
    public function getAllAsArray(): array
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('p.id')
            ->addSelect('p.name')
            ->from(Position::class, 'p');
        return \array_column($query_builder->getQuery()->getResult(), 'name', 'id');
    }
}
