<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ForumDiscussion as ForumDiscussionEntity;
use App\Entity\ForumForum;
use App\Entity\ForumPost;
use App\Entity\User;
use App\Form\ForumPost as ForumPostForm;
use App\Generics\DateGenerics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\Persistence\ManagerRegistry;

class ForumDiscussionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumDiscussionEntity::class);
    }

    /**
     * @throws \Exception
     */
    public function findForDashboard(int $limit, ?User $user = null): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $max_query = '
            SELECT p.discussionid AS disc_id, MAX(p.timestamp) AS max_date_time
            FROM somda_forum_posts p
            JOIN somda_forum_discussion d ON d.discussionid = p.discussionid
            WHERE p.timestamp > :min_date
            GROUP BY disc_id';
        if (null === $user) {
            $statement = $connection->prepare('
                SELECT `d`.`discussionid` AS `id`, `d`.`title` AS `title`, `a`.`uid` AS `author_id`,
                    `a`.`username` AS `author_username`, `d`.`locked` AS `locked`, `d`.`viewed` AS `viewed`,
                    `f`.`type` AS `forum_type`, TRUE AS `discussion_read`, 0 AS `post_last_read`, `p_max`.`timestamp` AS `max_post_timestamp`,
                    (SELECT COUNT(*) FROM somda_forum_posts p_count WHERE p_count.discussionid = d.discussionid) AS `posts`
                FROM somda_forum_discussion d
                JOIN somda_forum_forums f ON f.forumid = d.forumid
                JOIN somda_users a ON a.uid = d.authorid
                JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
                INNER JOIN ('.$max_query.') m ON m.disc_id = d.discussionid
                WHERE p_max.timestamp = m.max_date_time AND f.type != :moderator_forum_type
                GROUP BY id, title, author_id, viewed, m.max_date_time, max_post_timestamp
                ORDER BY m.max_date_time DESC
                LIMIT 0, '.$limit);
        } else {
            $statement = $connection->prepare('
                SELECT `d`.`discussionid` AS `id`, `d`.`title` AS `title`, `a`.`uid` AS `author_id`,
                    `a`.`username` AS `author_username`, `d`.`locked` AS `locked`, `d`.`viewed` AS `viewed`,
                    `f`.`type` AS `forum_type`, IF(IFNULL(`r`.`postid`, 0) < p_max.postid, FALSE, TRUE) AS `discussion_read`,
					IFNULL(`r`.`postid`, 0) AS `post_last_read`,
                    `p_max`.`timestamp` AS `max_post_timestamp`,
                    (SELECT COUNT(*) FROM somda_forum_posts p_count WHERE p_count.discussionid = d.discussionid) AS `posts`
                FROM somda_forum_discussion d
                JOIN somda_forum_forums f ON f.forumid = d.forumid
                JOIN somda_users a ON a.uid = d.authorid
                JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
                LEFT JOIN somda_forum_last_read r
                    ON r.uid = :user_id AND r.discussionid = d.discussionid
                INNER JOIN ('.$max_query.') m ON m.disc_id = d.discussionid
                WHERE p_max.timestamp = m.max_date_time AND f.type != :moderator_forum_type
                GROUP BY `id`, `title`, `author_id`, `viewed`, m.max_date_time, `discussion_read`, `max_post_timestamp`
                ORDER BY m.max_date_time DESC
                LIMIT 0, '.$limit);
            $statement->bindValue('user_id', $user->id);
        }
        $statement->bindValue('min_date', \date(
            DateGenerics::DATE_FORMAT_DATABASE,
            \mktime(0, 0, 0, (int) \date('m'), (int) \date('d') - 30, (int) \date('Y'))
        ));
        $statement->bindValue('moderator_forum_type', ForumForum::TYPE_MODERATORS_ONLY);
        
        try {
            return $statement->executeQuery()->fetchAllAssociative();
        } catch (DBALDriverException) {
            return [];
        }
    }

    public function findByForum(ForumForum $forum, ?User $user = null, ?int $limit = null): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $max_query = '
            SELECT p.discussionid AS disc_id, MAX(p.timestamp) AS max_date_time
            FROM somda_forum_posts p
            JOIN somda_forum_discussion d ON d.discussionid = p.discussionid
            WHERE d.forumid = :forum_id
            GROUP BY disc_id';
        if (null === $user) {
            $statement = $connection->prepare('
                SELECT `d`.`discussionid` AS `id`, `d`.`title` AS `title`, `a`.`uid` AS `author_id`,
                    `a`.`username` AS `author_username`, `d`.`locked` AS `locked`, `d`.`viewed` AS `viewed`,
                    TRUE AS `discussion_read`, 0 AS `post_last_read`, `p_max`.`timestamp` AS `max_post_timestamp`,
                    (SELECT COUNT(*) FROM somda_forum_posts p_count WHERE p_count.discussionid = d.discussionid) AS `posts`
                FROM somda_forum_discussion d
                JOIN somda_users a ON a.uid = d.authorid
                JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
                INNER JOIN ('.$max_query.') m ON m.disc_id = d.discussionid
                WHERE d.forumid = :forum_id AND p_max.timestamp = m.max_date_time
                GROUP BY id, title, author_id, viewed, m.max_date_time, max_post_timestamp
                ORDER BY m.max_date_time DESC'.(null !== $limit ? ' LIMIT 0, '.$limit : ''));
        } else {
            $statement = $connection->prepare('
                SELECT `d`.`discussionid` AS `id`, `d`.`title` AS `title`, `a`.`uid` AS `author_id`,
                    `a`.`username` AS `author_username`, `d`.`locked` AS `locked`, `d`.`viewed` AS `viewed`,
                    IF(IFNULL(`r`.`postid`, 0) < p_max.postid, FALSE, TRUE) AS `discussion_read`,
					IFNULL(`r`.`postid`, 0) AS `post_last_read`,
                    `p_max`.`timestamp` AS `max_post_timestamp`,
                    (SELECT COUNT(*) FROM somda_forum_posts p_count WHERE p_count.discussionid = d.discussionid) AS `posts`
                FROM somda_forum_discussion d
                JOIN somda_users a ON a.uid = d.authorid
                JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
                LEFT JOIN somda_forum_last_read r
                    ON r.uid = :user_id AND r.discussionid = d.discussionid
                INNER JOIN ('.$max_query.') m ON m.disc_id = d.discussionid
                WHERE d.forumid = :forum_id AND p_max.timestamp = m.max_date_time
                GROUP BY `id`, `title`, `author_id`, `viewed`, m.max_date_time, `discussion_read`, `max_post_timestamp`
                ORDER BY m.max_date_time DESC'.(null !== $limit ? ' LIMIT 0, '.$limit : ''));
            $statement->bindValue('user_id', $user->id);
        }

        $statement->bindValue('forum_id', $forum->id);
        try {
            return $statement->executeQuery()->fetchAllAssociative();
        } catch (DBALDriverException | DBALException) {
            return [];
        }
    }

    public function findByFavorites(User $user): array
    {
        $max_query = '
            SELECT p.discussionid AS disc_id, MAX(p.timestamp) AS max_date_time
            FROM somda_forum_posts p
            JOIN somda_forum_discussion d ON d.discussionid = p.discussionid
            INNER JOIN somda_forum_favorites f ON f.discussionid = d.discussionid AND f.uid = :user_id
            GROUP BY disc_id';
        $query = '
            SELECT `d`.`discussionid` AS `id`, `d`.`title` AS `title`, `a`.`uid` AS `author_id`,
                `f`.`alerting` AS `alerting`,
                `a`.`username` AS `author_username`, `d`.`locked` AS `locked`, `d`.`viewed` AS `viewed`,
                IF(IFNULL(`r`.`postid`, 0) < p_max.postid, FALSE, TRUE) AS `discussion_read`,
				IFNULL(`r`.`postid`, 0) AS `post_last_read`,
                `p_max`.`timestamp` AS `max_post_timestamp`,
                (SELECT COUNT(*) FROM somda_forum_posts p_count WHERE p_count.discussionid = d.discussionid) AS `posts`
            FROM somda_forum_discussion d
            JOIN somda_users a ON a.uid = d.authorid
            JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
            INNER JOIN somda_forum_favorites f ON f.discussionid = d.discussionid AND f.uid = :user_id
            LEFT JOIN somda_forum_last_read r
                    ON r.uid = :user_id AND r.discussionid = d.discussionid
            INNER JOIN ('.$max_query.') m ON m.disc_id = d.discussionid
            WHERE p_max.timestamp = m.max_date_time
            GROUP BY `id`, `title`, `author_id`, `viewed`, m.max_date_time, `discussion_read`, `max_post_timestamp`
            ORDER BY m.max_date_time DESC';
        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('user_id', $user->id);

            return $statement->executeQuery()->fetchAllAssociative();
        } catch (DBALDriverException | DBALException) {
            return [];
        }
    }

    public function findUnread(User $user): array
    {
        $max_query = '
            SELECT p.discussionid AS disc_id, MAX(p.timestamp) AS max_date_time
            FROM somda_forum_posts p
            JOIN somda_forum_discussion d ON d.discussionid = p.discussionid
            WHERE p.timestamp > :min_date
            GROUP BY disc_id';
        $query = '
            SELECT `d`.`discussionid` AS `id`, `d`.`title` AS `title`, `a`.`uid` AS `author_id`,
                `a`.`username` AS `author_username`, `d`.`locked` AS `locked`, `d`.`viewed` AS `viewed`,
                FALSE AS `discussion_read`,
				IFNULL(`r`.`postid`, 0) AS `post_last_read`,
                `p_max`.`timestamp` AS `max_post_timestamp`,
                (SELECT COUNT(*) FROM somda_forum_posts p_count WHERE p_count.discussionid = d.discussionid) AS `posts`
            FROM somda_forum_discussion d
            JOIN somda_forum_forums f ON f.forumid = d.forumid
            JOIN somda_users a ON a.uid = d.authorid
            JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
            LEFT JOIN somda_forum_last_read r
                    ON r.uid = :user_id AND r.discussionid = d.discussionid
            INNER JOIN ('.$max_query.') m ON m.disc_id = d.discussionid
            WHERE p_max.timestamp = m.max_date_time AND f.type != :moderator_forum_type AND (`r`.`postid` IS NULL OR `r`.`postid` < p_max.postid)
            GROUP BY `id`, `title`, `author_id`, `viewed`, m.max_date_time, `discussion_read`, `max_post_timestamp`
            ORDER BY m.max_date_time DESC';

        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('min_date', \date(
                DateGenerics::DATE_FORMAT_DATABASE,
                \mktime(0, 0, 0, (int) \date('m'), (int) \date('d') - 1000, (int) \date('Y'))
            ));
            $statement->bindValue('moderator_forum_type', ForumForum::TYPE_MODERATORS_ONLY);
            $statement->bindValue('user_id', $user->id);

            return $statement->executeQuery()->fetchAllAssociative();
        } catch (DBALDriverException) {
            return [];
        }
    }

    /**
     * @throws \Exception|DBALDriverException
     */
    public function findLastDiscussion(): ?array
    {
        $max_query = '
            SELECT p.discussionid AS disc_id, MAX(p.timestamp) AS max_date_time
            FROM somda_forum_posts p
            JOIN somda_forum_discussion d ON d.discussionid = p.discussionid
            WHERE p.timestamp > :minDate
            GROUP BY disc_id';
        $query = '
            SELECT `d`.`discussionid` AS `id`, `d`.`title` AS `title`, `d`.`locked` AS `locked`,
                `p_max`.`timestamp` AS `max_post_timestamp`
            FROM somda_forum_discussion d
            JOIN somda_forum_forums f ON f.forumid = d.forumid
            JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
            INNER JOIN ('.$max_query.') m ON m.disc_id = d.discussionid
            WHERE p_max.timestamp = m.max_date_time AND f.type != :moderator_forum_type
            GROUP BY id, title, max_post_timestamp
            ORDER BY m.max_date_time DESC
            LIMIT 1';

        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindValue('minDate', \date(
            DateGenerics::DATE_FORMAT_DATABASE,
            \mktime(0, 0, 0, (int) \date('m'), (int) \date('d') - 1000, (int) \date('Y'))
        ));
        $statement->bindValue('moderator_forum_type', ForumForum::TYPE_MODERATORS_ONLY);
        $last_discussion = $statement->executeQuery()->fetchAssociative();

        return $last_discussion === false ? null : $last_discussion;
    }

    public function getNumberOfPosts(ForumDiscussionEntity $discussion): int
    {
        $query_builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(ForumPost::class, 'p')
            ->andWhere('p.discussion = :'.ForumPostForm::FIELD_DISCUSSION)
            ->setParameter(ForumPostForm::FIELD_DISCUSSION, $discussion)
            ->setMaxResults(1);
        try {
            return (int) $query_builder->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }

    public function getPostNumberInDiscussion(ForumDiscussionEntity $discussion, int $post_id): int
    {
        $query = '
            SELECT COUNT(*) AS position
            FROM somda_forum_posts p2
            INNER JOIN somda_forum_posts p1 ON p1.postid = :post_id
            WHERE p2.discussionid = :discussion_id
              AND (p2.timestamp < p1.timestamp OR (p2.timestamp = p1.timestamp AND p2.postid < p1.postid))';

        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindValue('discussion_id', $discussion->id);
        $statement->bindValue('post_id', $post_id);

        return (int) $statement->executeQuery()->fetchOne();
    }

    public function getNumberOfReadPosts(ForumDiscussionEntity $discussion, User $user): int
    {
       $query = 'SELECT COUNT(`p`.`postid`) AS `number`
            FROM `somda_forum_posts` `p`
            LEFT JOIN `somda_forum_last_read` `r` ON `r`.`uid` = :user_id AND `r`.`discussionid` = `p`.`discussionid`
            WHERE `p`.`discussionid` = :discussion_id AND `p`.`postid` <= IFNULL(`r`.`postid`, 0)';

        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindValue('user_id', $user->id);
        $statement->bindValue('discussion_id', $discussion->id);

        $result = $statement->executeQuery()->fetchAssociative();

        return $result === false ? 0 : $result['number'];
    }

    /**
     * @param ForumPost[] $posts
     */
    public function markPostsAsRead(User $user, ForumDiscussionEntity $discussion, array $posts): void
    {
        $max_post_id = \array_reduce($posts, static fn(int $max, ForumPost $post) => \max($max, $post->id ?? 0), 0);
        $query = 'REPLACE INTO `somda_forum_last_read` (`uid`, `discussionid`, `postid`) VALUES (:user_id, :discussion_id, :max_post_id)';

        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindValue('user_id', $user->id);
        $statement->bindValue('discussion_id', $discussion->id);
        $statement->bindValue('max_post_id', $max_post_id);

        try {
            $statement->executeStatement();
        } catch (DBALDriverException | DBALException) {
            return;
        }
    }

    public function markAllPostsAsRead(User $user): void
    {
        $query = '
            REPLACE INTO `somda_forum_last_read` (`uid`, `discussionid`, `postid`)
            SELECT :user_id, `d`.`discussionid`, MAX(`p`.`postid`)
            FROM `somda_forum_discussion` `d`
            JOIN `somda_forum_posts` `p` ON `p`.`discussionid` = `d`.`discussionid`
            GROUP BY `d`.`discussionid`';

        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('user_id', $user->id);
            $statement->executeStatement();
        } catch (DBALDriverException | DBALException) {
            return;
        }
    }
}
