<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\ForumSearchWord as ForumSearchWordEntity;
use App\Model\ForumSearchResult;
use Doctrine\ORM\EntityRepository;

class ForumSearchWord extends EntityRepository
{
    /**
     * @param ForumSearchWordEntity[] $words
     * @return ForumSearchResult[]
     */
    public function searchByWords(array $words): array
    {
        $wordIdList = [];
        foreach ($words as $word) {
            $wordIdList[] = $word->id;
        }

        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->addSelect('l.title AS titleMatch')
            ->addSelect('d.id AS discussionId')
            ->addSelect('d.title AS discussionTitle')
            ->addSelect('d.locked AS discussionLocked')
            ->addSelect('u.id AS authorId')
            ->addSelect('u.username AS authorUsername')
            ->addSelect('p.id AS postId')
            ->addSelect('p.timestamp AS postTimestamp')
            ->from(ForumSearchWordEntity::class, 'w')
            ->join('w.lists', 'l')
            ->join('l.post', 'p')
            ->join('p.author', 'u')
            ->join('p.discussion', 'd')
            ->andWhere('w.id IN (:wordIdList)')
            ->setParameter('wordIdList', $wordIdList)
            ->addOrderBy('l.title', 'DESC')
            ->addOrderBy('p.timestamp', 'DESC');
        $queryResults = $queryBuilder->getQuery()->getArrayResult();

        $results = [];
        foreach ($queryResults as $queryResult) {
            $results[] = new ForumSearchResult($queryResult);
        }
        return $results;
    }
}
