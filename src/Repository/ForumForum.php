<?php

namespace App\Repository;

use App\Entity\ForumCategory;
use App\Entity\User;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

class ForumForum extends EntityRepository
{
    /**
     * @param ForumCategory $category
     * @return array
     */
    public function findByCategory(ForumCategory $category, User $user = null): array
    {
        if (is_null($user)) {
            $query = '
                SELECT `f`.`forumid` AS `id`,
                    `f`.`name` AS `name`,
                    COUNT(DISTINCT(`d`.`discussionid`)) AS `numberOfDiscussions`,
                    TRUE AS `forum_read`
                FROM `somda_forum_forums` `f`
                JOIN `somda_forum_discussion` `d` ON `d`.`forumid` = `f`.`forumid`
                JOIN `somda_forum_posts` `p` ON `p`.`discussionid` = `d`.`discussionid`
                WHERE `f`.`catid` = :categoryId
                GROUP BY `f`.`forumid`
                ORDER BY `f`.`volgorde` ASC';
        } else {
            $query = '
                SELECT `f`.`forumid` AS `id`,
                    `f`.`name` AS `name`,
                    COUNT(DISTINCT(`d`.`discussionid`)) AS `numberOfDiscussions`,
                    IF(`r`.`postid` IS NULL, FALSE, TRUE) AS `forum_read`
                FROM `somda_forum_forums` `f`
                JOIN `somda_forum_discussion` `d` ON `d`.`forumid` = `f`.`forumid`
                JOIN `somda_forum_posts` `p` ON `p`.`discussionid` = `d`.`discussionid`
                LEFT JOIN `somda_forum_read_' . substr($user->getId(), -1) . '` `r`
                    ON `r`.`uid` = ' . $user->getId() . ' AND `r`.`postid` = `p`.`postid`
                WHERE `f`.`catid` = :categoryId
                GROUP BY `f`.`forumid`
                ORDER BY `f`.`volgorde` ASC';
        }

        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->bindValue('categoryId', $category->getId());
            $statement->execute();
            return $statement->fetchAll();
        } catch (DBALException $exception) {
            return [];
        }
    }
}
