<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ForumSearchWord;
use App\Model\ForumSearchResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ForumSearchWord>
 */
class ForumSearchWordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumSearchWord::class);
    }

    /**
     * @param ForumSearchWord[] $words
     * @return ForumSearchResult[]
     */
    public function searchByWords(array $words): array
    {
        $word_id_list = [];
        foreach ($words as $word) {
            $word_id_list[] = $word->id;
        }

        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->addSelect('l.title AS title_match')
            ->addSelect('d.id AS discussion_id')
            ->addSelect('d.title AS discussion_title')
            ->addSelect('d.locked AS discussion_locked')
            ->addSelect('u.id AS author_id')
            ->addSelect('u.username AS author_username')
            ->addSelect('p.id AS post_id')
            ->addSelect('p.timestamp AS post_timestamp')
            ->from(ForumSearchWord::class, 'w')
            ->join('w.lists', 'l')
            ->join('l.post', 'p')
            ->join('p.author', 'u')
            ->join('p.discussion', 'd')
            ->andWhere('w.id IN (:word_id_list)')
            ->setParameter('word_id_list', $word_id_list)
            ->addOrderBy('l.title', 'DESC')
            ->addOrderBy('p.timestamp', 'DESC');
        $query_results = $query_builder->getQuery()->getArrayResult();

        $results = [];
        foreach ($query_results as $query_result) {
            $results[] = new ForumSearchResult($query_result);
        }
        return $results;
    }
}
