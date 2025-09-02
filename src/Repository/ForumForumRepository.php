<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ForumForum as ForumForumEntity;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;
use ErrorException;

class ForumForumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumForumEntity::class);
    }

    public function findAllAndGetArray(?int $user_id = null): array
    {
        $query = '
            SELECT `c`.`catid` AS `categoryId`, `c`.`name` AS `categoryName`, `c`.`volgorde` AS `categoryOrder`,
                `f`.`forumid` AS `id`, `f`.`name` AS `name`, `f`.`description` AS `description`, `f`.`type` AS `type`, `f`.`volgorde` AS `order`,
                IF(`m`.`uid` = :user_id, TRUE, FALSE) AS `user_is_moderator`,
                COUNT(DISTINCT(`d`.`discussionid`)) AS `number_of_discussions`
            FROM `somda_forum_forums` `f`
            JOIN `somda_forum_cats` `c` ON `c`.`catid` = `f`.`catid`
            LEFT JOIN `somda_forum_discussion` `d` ON `d`.`forumid` = `f`.`forumid`
            LEFT JOIN `somda_forum_mods` `m` ON `m`.`forumid` = `f`.`forumid` AND `m`.`uid` = :user_id
            GROUP BY `f`.`forumid`';

        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('user_id', $user_id, ParameterType::INTEGER);
            return $statement->executeQuery()->fetchAllAssociative();
        } catch (DBALException | DBALDriverException) {
            return [];
        }
    }

    public function getNumberOfUnreadDiscussionsInForum(int $forum_id, User $user): int
    {
        $max_query = '
            SELECT `p`.`discussionid` AS `disc_id`, MAX(`p`.`timestamp`) AS `max_date_time`
            FROM `somda_forum_posts` `p`
            JOIN `somda_forum_discussion` `d` ON `d`.`discussionid` = `p`.`discussionid`
            WHERE `d`.`forumid` = :forum_id
            GROUP BY `disc_id`';
        $query = '
            SELECT SUM(IF(IFNULL(`r`.`postid`, 0) < `p_max`.`postid`, 1, 0)) AS `number_of_discussions_unread`
            FROM `somda_forum_discussion` `d`
            JOIN `somda_users` `a` ON `a`.`uid` = `d`.`authorid`
            JOIN `somda_forum_posts` `p_max` ON `p_max`.`discussionid` = `d`.`discussionid`
            LEFT JOIN `somda_forum_last_read` `r` ON `r`.`uid` = :user_id AND `r`.`discussionid` = `d`.`discussionid`
            INNER JOIN ('.$max_query.') `m` ON `m`.`disc_id` = `d`.`discussionid`
            WHERE `d`.`forumid` = :forum_id AND `p_max`.`timestamp` = `m`.`max_date_time`
            GROUP BY `d`.`forumid`';
        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('forum_id', $forum_id, ParameterType::INTEGER);
            $statement->bindValue('user_id', $user->id, ParameterType::INTEGER);
            return (int) $statement->executeQuery()->fetchOne();
        } catch (DBALException | DBALDriverException | ErrorException) {
            return 0;
        }
    }
}
