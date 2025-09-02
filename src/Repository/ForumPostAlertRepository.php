<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ForumPostAlert as ForumPostAlertEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ForumPostAlertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumPostAlertEntity::class);
    }

    /**
     * @return int
     */
    public function getNumberOfOpenAlerts(): int
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(a.id)')
            ->from(ForumPostAlertEntity::class, 'a')
            ->andWhere('a.closed = FALSE')
            ->setMaxResults(1);
        try {
            return (int) $query_builder->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * @return ForumPostAlertEntity[]
     */
    public function findForOverview(): array
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('f.closed')
            ->addSelect('GROUP_CONCAT(DISTINCT u.id ORDER BY u.id SEPARATOR \',\') AS sender_ids')
            ->addSelect('GROUP_CONCAT(DISTINCT u.username ORDER BY u.id SEPARATOR \',\') AS sender_usernames')
            ->addSelect('d.id AS discussion_id')
            ->addSelect('d.title AS discussion_title')
            ->addSelect('p.id AS post_id')
            ->addSelect('COUNT(DISTINCT(f.id)) AS number_of_alerts')
            ->addSelect('COUNT(DISTINCT(n.id)) AS number_of_notes')
            ->from(ForumPostAlertEntity::class, 'f')
            ->join('f.sender', 'u')
            ->join('f.post', 'p')
            ->join('p.discussion', 'd')
            ->leftJoin('f.notes', 'n')
            ->addGroupBy('f.post')
            ->addOrderBy('f.closed', 'ASC')
            ->addOrderBy('f.timestamp', 'DESC')
            ->setMaxResults(100);
        return $query_builder->getQuery()->getArrayResult();
    }
}
