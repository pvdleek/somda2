<?php

namespace App\Repository;

use App\Entity\ForumPostAlert as ForumPostAlertEntity;
use Doctrine\ORM\EntityRepository;
use Exception;

class ForumPostAlert extends EntityRepository
{
    /**
     * @return int
     */
    public function getNumberOfOpenAlerts(): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(a.id)')
            ->from(ForumPostAlertEntity::class, 'a')
            ->andWhere('a.closed = FALSE')
            ->setMaxResults(1);
        try {
            return (int)$queryBuilder->getQuery()->getSingleScalarResult();
        } catch (Exception $exception) {
            return 0;
        }
    }

    /**
     * @return ForumPostAlertEntity[]
     */
    public function findForOverview(): array
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('f.closed')
            ->addSelect('GROUP_CONCAT(DISTINCT u.id ORDER BY u.id SEPARATOR \',\') AS senderIds')
            ->addSelect('GROUP_CONCAT(DISTINCT u.username ORDER BY u.id SEPARATOR \',\') AS senderUsernames')
            ->addSelect('d.id AS discussionId')
            ->addSelect('d.title AS discussionTitle')
            ->addSelect('p.id AS postId')
            ->addSelect('COUNT(DISTINCT(f.id)) AS numberOfAlerts')
            ->addSelect('COUNT(DISTINCT(n.id)) AS numberOfNotes')
            ->from(ForumPostAlertEntity::class, 'f')
            ->join('f.sender', 'u')
            ->join('f.post', 'p')
            ->join('p.discussion', 'd')
            ->leftJoin('f.notes', 'n')
            ->addGroupBy('f.post')
            ->addOrderBy('f.closed', 'ASC')
            ->addOrderBy('f.timestamp', 'DESC')
            ->setMaxResults(100);
        return $queryBuilder->getQuery()->getArrayResult();
    }
}
