<?php

namespace App\Repository;

use App\Entity\ForumDiscussion as ForumDiscussionEntity;
use App\Entity\ForumForum;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

class ForumDiscussion extends EntityRepository
{
    /**
     * @param ForumForum $forum
     * @return ForumDiscussionEntity[]
     */
    public function findByForum(ForumForum $forum) : array
    {
        $maxQuery = '
            SELECT p.discussionid AS disc_id, MAX(p.timestamp) AS max_date_time
            FROM somda_forum_posts p
            JOIN somda_forum_discussion d ON d.discussionid = p.discussionid
            WHERE d.forumid = :forumid
            GROUP BY disc_id';
        $query = '
            SELECT d.discussionid AS id, d.title AS title, a.uid AS author_id, a.username AS author_username,
                d.viewed AS viewed, p_max.timestamp AS max_post_timestamp, COUNT(*) AS posts
            FROM somda_forum_discussion d
            JOIN somda_users a ON a.uid = d.authorid
            JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
            JOIN somda_forum_posts p_count ON p_count.discussionid = d.discussionid
            INNER JOIN (' . $maxQuery . ') m ON m.disc_id = d.discussionid
            WHERE d.forumid = :forumid AND p_max.timestamp = m.max_date_time
            GROUP BY id, title, author_id, viewed, m.max_date_time, max_post_timestamp
            ORDER BY m.max_date_time DESC';
        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('forumid', $forum->getId());
            $statement->execute();
            return $statement->fetchAll();
        } catch (DBALException $exception) {
            return [];
        }
    }
}
