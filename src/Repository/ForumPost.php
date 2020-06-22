<?php

namespace App\Repository;

use App\Entity\ForumPost as ForumPostEntity;
use Doctrine\ORM\EntityRepository;

class ForumPost extends EntityRepository
{
    /**
     * @return int[]
     */
    public function findAllAndGetIds(): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('p.id')
            ->from(ForumPostEntity::class, 'p');
        return array_column($queryBuilder->getQuery()->getResult(), 'id');
    }
}
