<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Block;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Block>
 */
class BlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Block::class);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getMenuStructure(): array
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('b.id AS id, b.name AS name, b.route AS route, b.role AS role')
            ->addSelect('parent.id AS parent_id, parent.name AS parent_name')
            ->from(Block::class, 'b')
            ->join('b.parent', 'parent')
            ->andWhere('parent.id > 0')
            ->addOrderBy('parent.menu_order', 'ASC')
            ->addOrderBy('b.menu_order', 'ASC');
        return $query_builder->getQuery()->getArrayResult();
    }
}
