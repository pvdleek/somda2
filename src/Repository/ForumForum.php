<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\ORM\EntityRepository;
use ErrorException;

class ForumForum extends EntityRepository
{
    /**
     * @param int|null $userId
     * @return array
     */
    public function findAllAndGetArray(?int $userId = null): array
    {
        $query = '
            SELECT `c`.`catid` AS `categoryId`, `c`.`name` AS `categoryName`, `c`.`volgorde` AS `categoryOrder`,
                `f`.`forumid` AS `id`, `f`.`name` AS `name`, `f`.`type` AS `type`, `f`.`volgorde` AS `order`,
                IF(`m`.`uid` = :userId, TRUE, FALSE) AS `userIsModerator`,
                COUNT(DISTINCT(`d`.`discussionid`)) AS `numberOfDiscussions`
            FROM `somda_forum_forums` `f`
            JOIN `somda_forum_cats` `c` ON `c`.`catid` = `f`.`catid`
            LEFT JOIN `somda_forum_discussion` `d` ON `d`.`forumid` = `f`.`forumid`
            LEFT JOIN `somda_forum_posts` `p` ON `p`.`discussionid` = `d`.`discussionid`
            LEFT JOIN `somda_forum_mods` `m` ON `m`.`forumid` = `f`.`forumid` AND `m`.`uid` = :userId
            GROUP BY `f`.`forumid`';

        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindParam('userId', $userId);
            $statement->execute();
            return $statement->fetchAllAssociative();
        } catch (DBALException | DBALDriverException $exception) {
            return [];
        }
    }

    /**
     * @param int $forumId
     * @param User $user
     * @return int
     */
    public function getNumberOfUnreadPostsInForum(int $forumId, User $user): int
    {
        $maxQuery = '
            SELECT p.discussionid AS disc_id, MAX(p.timestamp) AS max_date_time
            FROM somda_forum_posts p
            JOIN somda_forum_discussion d ON d.discussionid = p.discussionid
            WHERE d.forumid = :forumId
            GROUP BY disc_id';
        $query = '
            SELECT SUM(IF(`r`.`postid` IS NULL, 1, 0)) AS `number_of_discussions_unread`
            FROM somda_forum_discussion d
            JOIN somda_users a ON a.uid = d.authorid
            JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
            LEFT JOIN somda_forum_read_' . substr((string)$user->id, -1) . ' r
                ON r.uid = ' . (string)$user->id . ' AND r.postid = p_max.postid
            INNER JOIN (' . $maxQuery . ') m ON m.disc_id = d.discussionid
            WHERE d.forumid = :forumId AND p_max.timestamp = m.max_date_time
            GROUP BY `d`.`forumid`';
        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('forumId', $forumId);
            $statement->execute();
            return (int)$statement->fetchFirstColumn()[0];
        } catch (DBALException | DBALDriverException | ErrorException $exception) {
            return 0;
        }
    }
}
