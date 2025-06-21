<?php

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

class ForumDiscussion extends ServiceEntityRepository
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
        $maxQuery = '
            SELECT p.discussionid AS disc_id, MAX(p.timestamp) AS max_date_time
            FROM somda_forum_posts p
            JOIN somda_forum_discussion d ON d.discussionid = p.discussionid
            WHERE p.timestamp > :minDate
            GROUP BY disc_id';
        if (null === $user) {
            $query = '
                SELECT `d`.`discussionid` AS `id`, `d`.`title` AS `title`, `a`.`uid` AS `author_id`,
                    `a`.`username` AS `author_username`, `d`.`locked` AS `locked`, `d`.`viewed` AS `viewed`,
                    `f`.`type` AS `forum_type`, TRUE AS `discussion_read`, 0 AS `post_last_read`, `p_max`.`timestamp` AS `max_post_timestamp`,
                    COUNT(*) AS `posts`
                FROM somda_forum_discussion d
                JOIN somda_forum_forums f ON f.forumid = d.forumid
                JOIN somda_users a ON a.uid = d.authorid
                JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
                JOIN somda_forum_posts p_count ON p_count.discussionid = d.discussionid
                INNER JOIN (' . $maxQuery . ') m ON m.disc_id = d.discussionid
                WHERE p_max.timestamp = m.max_date_time AND f.type != :moderatorForumType
                GROUP BY id, title, author_id, viewed, m.max_date_time, max_post_timestamp
                ORDER BY m.max_date_time DESC
                LIMIT 0, ' . $limit;
        } else {
            $query = '
                SELECT `d`.`discussionid` AS `id`, `d`.`title` AS `title`, `a`.`uid` AS `author_id`,
                    `a`.`username` AS `author_username`, `d`.`locked` AS `locked`, `d`.`viewed` AS `viewed`,
                    `f`.`type` AS `forum_type`, IF(IFNULL(`r`.`postid`, 0) < p_max.postid, FALSE, TRUE) AS `discussion_read`,
					IFNULL(`r`.`postid`, 0) AS `post_last_read`,
                    `p_max`.`timestamp` AS `max_post_timestamp`, COUNT(*) AS `posts`
                FROM somda_forum_discussion d
                JOIN somda_forum_forums f ON f.forumid = d.forumid
                JOIN somda_users a ON a.uid = d.authorid
                JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
                LEFT JOIN somda_forum_last_read r
                    ON r.uid = ' . (string) $user->id . ' AND r.discussionid = d.discussionid
                JOIN somda_forum_posts p_count ON p_count.discussionid = d.discussionid
                INNER JOIN (' . $maxQuery . ') m ON m.disc_id = d.discussionid
                WHERE p_max.timestamp = m.max_date_time AND f.type != :moderatorForumType
                GROUP BY `id`, `title`, `author_id`, `viewed`, m.max_date_time, `discussion_read`, `max_post_timestamp`
                ORDER BY m.max_date_time DESC
                LIMIT 0, ' . $limit;
        }

        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue(
                'minDate',
                \date(DateGenerics::DATE_FORMAT_DATABASE, \mktime(0, 0, 0, \date('m'), \date('d') - 30, \date('Y')))
            );
            $statement->bindValue('moderatorForumType', ForumForum::TYPE_MODERATORS_ONLY);
            return $statement->executeQuery()->fetchAllAssociative();
        } catch (DBALDriverException) {
            return [];
        }
    }

    public function findByForum(ForumForum $forum, ?User $user = null, ?int $limit = null): array
    {
        $maxQuery = '
            SELECT p.discussionid AS disc_id, MAX(p.timestamp) AS max_date_time
            FROM somda_forum_posts p
            JOIN somda_forum_discussion d ON d.discussionid = p.discussionid
            WHERE d.forumid = :forumid
            GROUP BY disc_id';
        if (null === $user) {
            $query = '
                SELECT `d`.`discussionid` AS `id`, `d`.`title` AS `title`, `a`.`uid` AS `author_id`,
                    `a`.`username` AS `author_username`, `d`.`locked` AS `locked`, `d`.`viewed` AS `viewed`,
                    TRUE AS `discussion_read`, 0 AS `post_last_read`, `p_max`.`timestamp` AS `max_post_timestamp`, COUNT(*) AS `posts`
                FROM somda_forum_discussion d
                JOIN somda_users a ON a.uid = d.authorid
                JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
                JOIN somda_forum_posts p_count ON p_count.discussionid = d.discussionid
                INNER JOIN (' . $maxQuery . ') m ON m.disc_id = d.discussionid
                WHERE d.forumid = :forumid AND p_max.timestamp = m.max_date_time
                GROUP BY id, title, author_id, viewed, m.max_date_time, max_post_timestamp
                ORDER BY m.max_date_time DESC';
        } else {
            $query = '
                SELECT `d`.`discussionid` AS `id`, `d`.`title` AS `title`, `a`.`uid` AS `author_id`,
                    `a`.`username` AS `author_username`, `d`.`locked` AS `locked`, `d`.`viewed` AS `viewed`,
                    IF(IFNULL(`r`.`postid`, 0) < p_max.postid, FALSE, TRUE) AS `discussion_read`,
					IFNULL(`r`.`postid`, 0) AS `post_last_read`,
                    `p_max`.`timestamp` AS `max_post_timestamp`, COUNT(*) AS `posts`
                FROM somda_forum_discussion d
                JOIN somda_users a ON a.uid = d.authorid
                JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
                LEFT JOIN somda_forum_last_read r
                    ON r.uid = ' . (string) $user->id . ' AND r.discussionid = d.discussionid
                JOIN somda_forum_posts p_count ON p_count.discussionid = d.discussionid
                INNER JOIN (' . $maxQuery . ') m ON m.disc_id = d.discussionid
                WHERE d.forumid = :forumid AND p_max.timestamp = m.max_date_time
                GROUP BY `id`, `title`, `author_id`, `viewed`, m.max_date_time, `discussion_read`, `max_post_timestamp`
                ORDER BY m.max_date_time DESC';
        }
        if (null !== $limit) {
            $query .= ' LIMIT 0, ' . $limit;
        }
        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('forumid', $forum->id);
            return $statement->executeQuery()->fetchAllAssociative();
        } catch (DBALDriverException | DBALException) {
            return [];
        }
    }

    public function findByFavorites(User $user): array
    {
        $maxQuery = '
            SELECT p.discussionid AS disc_id, MAX(p.timestamp) AS max_date_time
            FROM somda_forum_posts p
            JOIN somda_forum_discussion d ON d.discussionid = p.discussionid
            INNER JOIN somda_forum_favorites f ON f.discussionid = d.discussionid AND f.uid = :userId
            GROUP BY disc_id';
        $query = '
            SELECT `d`.`discussionid` AS `id`, `d`.`title` AS `title`, `a`.`uid` AS `author_id`,
                `f`.`alerting` AS `alerting`,
                `a`.`username` AS `author_username`, `d`.`locked` AS `locked`, `d`.`viewed` AS `viewed`,
                IF(IFNULL(`r`.`postid`, 0) < p_max.postid, FALSE, TRUE) AS `discussion_read`,
				IFNULL(`r`.`postid`, 0) AS `post_last_read`,
                `p_max`.`timestamp` AS `max_post_timestamp`, COUNT(*) AS `posts`
            FROM somda_forum_discussion d
            JOIN somda_users a ON a.uid = d.authorid
            JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
            INNER JOIN somda_forum_favorites f ON f.discussionid = d.discussionid AND f.uid = :userId
            LEFT JOIN somda_forum_last_read r
                    ON r.uid = ' . (string) $user->id . ' AND r.discussionid = d.discussionid
            JOIN somda_forum_posts p_count ON p_count.discussionid = d.discussionid
            INNER JOIN (' . $maxQuery . ') m ON m.disc_id = d.discussionid
            WHERE p_max.timestamp = m.max_date_time
            GROUP BY `id`, `title`, `author_id`, `viewed`, m.max_date_time, `discussion_read`, `max_post_timestamp`
            ORDER BY m.max_date_time DESC';
        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('userId', $user->id);
            return $statement->executeQuery()->fetchAllAssociative();
        } catch (DBALDriverException | DBALException) {
            return [];
        }
    }

    public function findUnread(User $user): array
    {
        $maxQuery = '
            SELECT p.discussionid AS disc_id, MAX(p.timestamp) AS max_date_time
            FROM somda_forum_posts p
            JOIN somda_forum_discussion d ON d.discussionid = p.discussionid
            WHERE p.timestamp > :minDate
            GROUP BY disc_id';
        $query = '
            SELECT `d`.`discussionid` AS `id`, `d`.`title` AS `title`, `a`.`uid` AS `author_id`,
                `a`.`username` AS `author_username`, `d`.`locked` AS `locked`, `d`.`viewed` AS `viewed`,
                FALSE AS `discussion_read`,
				IFNULL(`r`.`postid`, 0) AS `post_last_read`,
                `p_max`.`timestamp` AS `max_post_timestamp`, COUNT(*) AS `posts`
            FROM somda_forum_discussion d
            JOIN somda_forum_forums f ON f.forumid = d.forumid
            JOIN somda_users a ON a.uid = d.authorid
            JOIN somda_forum_posts p_max ON p_max.discussionid = d.discussionid
            LEFT JOIN somda_forum_last_read r
                    ON r.uid = ' . (string) $user->id . ' AND r.discussionid = d.discussionid
            JOIN somda_forum_posts p_count ON p_count.discussionid = d.discussionid
            INNER JOIN (' . $maxQuery . ') m ON m.disc_id = d.discussionid
            WHERE p_max.timestamp = m.max_date_time AND f.type != :moderatorForumType AND (`r`.`postid` IS NULL OR `r`.`postid` < p_max.postid)
            GROUP BY `id`, `title`, `author_id`, `viewed`, m.max_date_time, `discussion_read`, `max_post_timestamp`
            ORDER BY m.max_date_time DESC';

        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('minDate', date(
                DateGenerics::DATE_FORMAT_DATABASE,
                \mktime(0, 0, 0, (int) \date('m'), (int) \date('d') - 1000, (int) \date('Y'))
            ));
            $statement->bindValue('moderatorForumType', ForumForum::TYPE_MODERATORS_ONLY);
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
        $maxQuery = '
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
            INNER JOIN (' . $maxQuery . ') m ON m.disc_id = d.discussionid
            WHERE p_max.timestamp = m.max_date_time AND f.type != :moderatorForumType
            GROUP BY id, title, max_post_timestamp
            ORDER BY m.max_date_time DESC
            LIMIT 1';

        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindValue(
            'minDate',
            \date(DateGenerics::DATE_FORMAT_DATABASE, \mktime(0, 0, 0, \date('m'), \date('d') - 5, \date('Y')))
        );
        $statement->bindValue('moderatorForumType', ForumForum::TYPE_MODERATORS_ONLY);
        $lastDiscussion = $statement->executeQuery()->fetchAssociative();

        return $lastDiscussion === false ? null : $lastDiscussion;
    }

    public function getNumberOfPosts(ForumDiscussionEntity $discussion): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(ForumPost::class, 'p')
            ->andWhere('p.discussion = :' . ForumPostForm::FIELD_DISCUSSION)
            ->setParameter(ForumPostForm::FIELD_DISCUSSION, $discussion)
            ->setMaxResults(1);
        try {
            return (int) $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }

    public function getPostNumberInDiscussion(ForumDiscussionEntity $discussion, int $postId): int
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('p.id')
            ->from(ForumPost::class, 'p')
            ->andWhere('p.discussion = :' . ForumPostForm::FIELD_DISCUSSION)
            ->setParameter(ForumPostForm::FIELD_DISCUSSION, $discussion)
            ->addOrderBy('p.timestamp', 'ASC');
        $postIds = \array_column($queryBuilder->getQuery()->getResult(), 'id');
        $position = \array_search($postId, $postIds);

        return $position === false ? 0 : $position;
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
		$max_post_id = 0;
        foreach ($posts as $post) {
            if ($post->id > $max_post_id) {
				$max_post_id = $post->id;
			}
        }
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
        $query = 'REPLACE INTO `somda_forum_last_read` (`uid`, `discussionid`, `postid`) ' .
            'SELECT ' . (string) $user->id . ' AS `uid`, `d`.`discussionid`, `p`.`postid` ' .
            'FROM `somda_forum_discussion` `d` ' .
            'LEFT JOIN `somda_forum_posts` `p` ON `p`.`postid` = (SELECT `postid` FROM `somda_forum_posts` ORDER BY `postid` DESC LIMIT 1)';

        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->executeStatement();
        } catch (DBALDriverException | DBALException) {
            return;
        }
    }
}
