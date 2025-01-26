<?php

namespace App\Repository;

use App\Entity\ForumForum as ForumForumEntity;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;
use ErrorException;

class ForumForum extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumForumEntity::class);
    }

    public function findAllAndGetArray(?int $userId = null): array
    {
        $query = '
            SELECT `c`.`catid` AS `categoryId`, `c`.`name` AS `categoryName`, `c`.`volgorde` AS `categoryOrder`,
                `f`.`forumid` AS `id`, `f`.`name` AS `name`, `f`.`description` AS `description`, `f`.`type` AS `type`, `f`.`volgorde` AS `order`,
                IF(`m`.`uid` = :userId, TRUE, FALSE) AS `userIsModerator`,
                COUNT(DISTINCT(`d`.`discussionid`)) AS `numberOfDiscussions`
            FROM `somda_forum_forums` `f`
            JOIN `somda_forum_cats` `c` ON `c`.`catid` = `f`.`catid`
            LEFT JOIN `somda_forum_discussion` `d` ON `d`.`forumid` = `f`.`forumid`
            LEFT JOIN `somda_forum_mods` `m` ON `m`.`forumid` = `f`.`forumid` AND `m`.`uid` = :userId
            GROUP BY `f`.`forumid`';

        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('userId', $userId, ParameterType::INTEGER);
            return $statement->executeQuery()->fetchAllAssociative();
        } catch (DBALException | DBALDriverException) {
            return [];
        }
    }

    public function getNumberOfUnreadDiscussionsInForum(int $forumId, User $user): int
    {
        $maxQuery = '
            SELECT `p`.`discussionid` AS `disc_id`, MAX(`p`.`timestamp`) AS `max_date_time`
            FROM `somda_forum_posts` `p`
            JOIN `somda_forum_discussion` `d` ON `d`.`discussionid` = `p`.`discussionid`
            WHERE `d`.`forumid` = :forumId
            GROUP BY `disc_id`';
        $query = '
            SELECT SUM(IF(IFNULL(`r`.`postid`, 0) < `p_max`.`postid`, 1, 0)) AS `number_of_discussions_unread`
            FROM `somda_forum_discussion` `d`
            JOIN `somda_users` `a` ON `a`.`uid` = `d`.`authorid`
            JOIN `somda_forum_posts` `p_max` ON `p_max`.`discussionid` = `d`.`discussionid`
            LEFT JOIN `somda_forum_last_read` `r` ON `r`.`uid` = :userId AND `r`.`discussionid` = `d`.`discussionid`
            INNER JOIN (' . $maxQuery . ') `m` ON `m`.`disc_id` = `d`.`discussionid`
            WHERE `d`.`forumid` = :forumId AND `p_max`.`timestamp` = `m`.`max_date_time`
            GROUP BY `d`.`forumid`';
        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('forumId', $forumId, ParameterType::INTEGER);
            $statement->bindValue('userId', $user->id, ParameterType::INTEGER);
            return (int) $statement->executeQuery()->fetchOne();
        } catch (DBALException | DBALDriverException | ErrorException) {
            return 0;
        }
    }
}
