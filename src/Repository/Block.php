<?php

namespace App\Repository;

use App\Entity\Block as BlockEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Block extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockEntity::class);
    }

    /**
     * @return array
     */
    public function getMenuStructure(): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('b.id AS id, b.name AS name, b.route AS route, b.role AS role')
            ->addSelect('parent.id AS parent_id, parent.name AS parent_name')
            ->from(BlockEntity::class, 'b')
            ->join('b.parent', 'parent')
            ->andWhere('parent.id > 0')
            ->addOrderBy('parent.menuOrder', 'ASC')
            ->addOrderBy('b.menuOrder', 'ASC');
        return $queryBuilder->getQuery()->getArrayResult();
    }
}
