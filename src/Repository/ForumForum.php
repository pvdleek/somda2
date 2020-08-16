<?php

namespace App\Repository;

use App\Entity\ForumForum as ForumForumEntity;
use App\Entity\User;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

class ForumForum extends EntityRepository
{
    /**
     * @return array
     */
    public function findAll(): array
    {
        $query = '
            SELECT `c`.`catid` AS `categoryId`, `c`.`name` AS `categoryName`, `c`.`volgorde` AS `categoryOrder`,
                `f`.`forumid` AS `id`, `f`.`name` AS `name`, `f`.`type` AS `type`, `f`.`volgorde` AS `order`,
                COUNT(DISTINCT(`d`.`discussionid`)) AS `numberOfDiscussions`
            FROM `somda_forum_forums` `f`
            JOIN `somda_forum_cats` `c` ON `c`.`catid` = `f`.`catid`
            JOIN `somda_forum_discussion` `d` ON `d`.`forumid` = `f`.`forumid`
            JOIN `somda_forum_posts` `p` ON `p`.`discussionid` = `d`.`discussionid`
            GROUP BY `f`.`forumid`';

        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->execute();
            return $statement->fetchAll();
        } catch (DBALException $exception) {
            return [];
        }
    }

    /**
     * @param ForumForumEntity $forum
     * @param User $user
     * @return int
     */
    public function getNumberOfUnreadPostsInForum(ForumForumEntity $forum, User $user): int
    {
        $maxQuery = '
            SELECT p.discussionid AS disc_id, MAX(p.timestamp) AS max_date_time
            FROM somda_forum_posts p
            JOIN somda_forum_discussion d ON d.discussionid = p.discussionid
            WHERE d.forumid = :forumid
            GROUP BY disc_id';
        $query = '
            SELECT SUM(IF(`r`.`postid` IS NULL, 1, 0)) AS `number_of_discussions_unread`
            FROM somda_forum_discussion d
            JOIN somda_users a ON a.uid = d.authorid
            JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
            LEFT JOIN somda_forum_read_' . substr($user->id, -1) . ' r
                ON r.uid = ' . $user->id . ' AND r.postid = p_max.postid
            INNER JOIN (' . $maxQuery . ') m ON m.disc_id = d.discussionid
            WHERE d.forumid = :forumid AND p_max.timestamp = m.max_date_time
            GROUP BY `d`.`forumid`';
        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('forumid', $forum->id);
            $statement->execute();
            return (int)$statement->fetchColumn(0);
        } catch (DBALException $exception) {
            return 0;
        }
    }
}
