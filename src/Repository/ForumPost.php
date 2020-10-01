<?php

namespace App\Repository;

use App\Entity\ForumPost as ForumPostEntity;
use App\Entity\User;
use Doctrine\DBAL\DBALException;
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

    /**
     * @param User $user
     * @return array
     */
    public function findByFavorites(User $user): array
    {
        $maxQuery = '
            SELECT p.discussionid AS disc_id, MAX(p.timestamp) AS max_date_time
            FROM somda_forum_posts p
            INNER JOIN fpf_forum_post_favorite f ON f.postid = p.postid AND f.uid = :userId
            GROUP BY disc_id';
        $query = '
            SELECT `p`.`postid` AS `id`, `t`.`text` AS `text`,
                `d`.`discussionid` AS `disc_id`, `d`.`title` AS `title`,
                `a`.`uid` AS `author_id`, `a`.`username` AS `author_username`,
                `p_max`.`timestamp` AS `max_post_timestamp`
            FROM somda_forum_posts p
            JOIN somda_forum_posts_text t ON t.postid = p.postid
            JOIN somda_forum_discussion d ON d.discussionid = p.discussionid
            JOIN somda_users a ON a.uid = p.authorid
            JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
            INNER JOIN fpf_forum_post_favorite f ON f.postid = p.postid AND f.uid = :userId
            INNER JOIN (' . $maxQuery . ') m ON m.disc_id = d.discussionid
            WHERE p_max.timestamp = m.max_date_time
            GROUP BY `id`, `disc_id`, `title`, `author_id`, `author_username`, `locked`, `max_post_timestamp`
            ORDER BY m.max_date_time DESC';
        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('userId', $user->id);
            $statement->execute();
            return $statement->fetchAll();
        } catch (DBALException $exception) {
            return [];
        }
    }

}
