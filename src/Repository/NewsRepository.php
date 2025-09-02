<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\News as NewsEntity;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\Persistence\ManagerRegistry;

class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsEntity::class);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findForDashboard(int $limit, ?User $user = null): array
    {
        if (null === $user) {
            $query = '
                SELECT `newsid` AS `id`, `title`, `timestamp`, TRUE AS `news_read`
                FROM somda_news
                WHERE archief = \'0\'
                GROUP BY `id`, `title`, `timestamp`, `news_read`
                ORDER BY `timestamp` DESC
                LIMIT 0, '.$limit;
        } else {
            $query = '
                SELECT `n`.`newsid` AS `id`, `n`.`title` AS `title`, `n`.`timestamp` AS `timestamp`,
                    IF(`r`.`uid` IS NULL, FALSE, TRUE) AS `news_read`
                FROM somda_news n
                LEFT JOIN somda_news_read r ON r.uid = '.(string) $user->id.' AND r.newsid = n.newsid
                WHERE n.archief = \'0\'
                GROUP BY `id`, `title`, `timestamp`, `news_read`
                ORDER BY `timestamp` DESC
                LIMIT 0, '.$limit;
        }
        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            return $statement->executeQuery()->fetchAllAssociative();
        } catch (DBALException | DBALDriverException) {
            return [];
        }
    }
}
