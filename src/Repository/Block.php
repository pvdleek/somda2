<?php

namespace App\Repository;

use App\Entity\Block as BlockEntity;
use Doctrine\ORM\EntityRepository;

class Block extends EntityRepository
{
    /**
     * @return array
     */
    public function getMenuStructure(): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('b.id AS id, b.name AS name, b.urlShort AS url, b.role AS role')
            ->addSelect('parent.id AS parent_id, parent.name AS parent_name, parent.urlShort as parent_url')
            ->from(BlockEntity::class, 'b')
            ->join('b.parent', 'parent')
            ->andWhere('parent.id > 0')
            ->addOrderBy('parent.menuOrder', 'ASC')
            ->addOrderBy('b.menuOrder', 'ASC');
        return $queryBuilder->getQuery()->getArrayResult();
    }
}
