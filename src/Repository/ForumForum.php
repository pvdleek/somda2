<?php

namespace App\Repository;

use App\Entity\ForumCategory;
use App\Entity\ForumForum as ForumForumEntity;
use Doctrine\ORM\EntityRepository;

class ForumForum extends EntityRepository
{
    /**
     * @param ForumCategory $category
     * @return array
     */
    public function findByCategory(ForumCategory $category): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('f.id AS id')
            ->addSelect('f.name AS name')
            ->addSelect('COUNT(d.id) AS numberOfDiscussions')
            ->from(ForumForumEntity::class, 'f')
            ->join('f.discussions', 'd')
            ->andWhere('f.category = :category')
            ->setParameter('category', $category)
            ->addGroupBy('f')
            ->addOrderBy('f.order', 'ASC');
        return $queryBuilder->getQuery()->getArrayResult();
    }
}
