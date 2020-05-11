<?php

namespace App\Repository;

use App\Entity\Position as PositionEntity;
use Doctrine\ORM\EntityRepository;

class Position extends EntityRepository
{
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
        return array_column($queryBuilder->getQuery()->getResult(), 'name', 'id');
    }
}
