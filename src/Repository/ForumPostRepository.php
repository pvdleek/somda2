<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ForumPost as ForumPostEntity;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\Persistence\ManagerRegistry;

class ForumPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumPostEntity::class);
    }

    /**
     * @param User $user
     * @return array
     */
    public function findByFavorites(User $user): array
    {
        $max_query = '
            SELECT p.discussionid AS disc_id, MAX(p.timestamp) AS max_date_time
            FROM somda_forum_posts p
            INNER JOIN fpf_forum_post_favorite f ON f.postid = p.postid AND f.uid = :user_id
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
            INNER JOIN fpf_forum_post_favorite f ON f.postid = p.postid AND f.uid = :user_id
            INNER JOIN ('.$max_query.') m ON m.disc_id = d.discussionid
            WHERE p_max.timestamp = m.max_date_time
            GROUP BY `id`, `disc_id`, `title`, `author_id`, `author_username`, `locked`, `max_post_timestamp`
            ORDER BY m.max_date_time DESC';
        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('user_id', $user->id);
            return $statement->executeQuery()->fetchAllAssociative();
        } catch (DBALException | DBALDriverException) {
            return [];
        }
    }
}
