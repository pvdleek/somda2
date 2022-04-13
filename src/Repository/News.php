<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\News as NewsEntity;
use App\Entity\User;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\ORM\EntityRepository;

class News extends EntityRepository
{
    /**
     * @param int $limit
     * @param User|null $user
     * @return NewsEntity[]
     */
    public function findForDashboard(int $limit, User $user = null): array
    {
        if (is_null($user)) {
            $query = '
                SELECT `newsid` AS `id`, `title`, `timestamp`, TRUE AS `news_read`
                FROM somda_news
                WHERE archief = \'0\'
                GROUP BY `id`, `title`, `timestamp`, `news_read`
                ORDER BY `timestamp` DESC
                LIMIT 0, ' . $limit;
        } else {
            $query = '
                SELECT `n`.`newsid` AS `id`, `n`.`title` AS `title`, `n`.`timestamp` AS `timestamp`,
                    IF(`r`.`uid` IS NULL, FALSE, TRUE) AS `news_read`
                FROM somda_news n
                LEFT JOIN somda_news_read r ON r.uid = ' . (string)$user->id . ' AND r.newsid = n.newsid
                WHERE n.archief = \'0\'
                GROUP BY `id`, `title`, `timestamp`, `news_read`
                ORDER BY `timestamp` DESC
                LIMIT 0, ' . $limit;
        }
        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            return $statement->executeQuery()->fetchAllAssociative();
        } catch (DBALException | DBALDriverException $exception) {
            return [];
        }
    }
}
