<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

class ForumForum extends EntityRepository
{
    /**
     * @param User $user
     * @return array
     */
    public function findAll(User $user = null): array
    {
        if (is_null($user)) {
            $query = '
                SELECT `c`.`catid` AS `categoryId`, `c`.`name` AS `categoryName`, `c`.`volgorde` AS `categoryOrder`,
                    `f`.`forumid` AS `id`, `f`.`name` AS `name`, `f`.`type` AS `type`, `f`.`volgorde` AS `order`,
                    COUNT(DISTINCT(`d`.`discussionid`)) AS `numberOfDiscussions`,
                    TRUE AS `forum_read`
                FROM `somda_forum_forums` `f`
                JOIN `somda_forum_cats` `c` ON `c`.`catid` = `f`.`catid`
                JOIN `somda_forum_discussion` `d` ON `d`.`forumid` = `f`.`forumid`
                JOIN `somda_forum_posts` `p` ON `p`.`discussionid` = `d`.`discussionid`
                GROUP BY `f`.`forumid`';
        } else {
            $query = '
                SELECT `c`.`catid` AS `categoryId`, `c`.`name` AS `categoryName`, `c`.`volgorde` AS `categoryOrder`,
                    `f`.`forumid` AS `id`, `f`.`name` AS `name`, `f`.`type` AS `type`, `f`.`volgorde` AS `order`,
                    COUNT(DISTINCT(`d`.`discussionid`)) AS `numberOfDiscussions`,
                    IF(SUM(`p`.`postid`) = SUM(`r`.`postid`), TRUE, FALSE) AS `forum_read`
                FROM `somda_forum_forums` `f`
                JOIN `somda_forum_cats` `c` ON `c`.`catid` = `f`.`catid`
                JOIN `somda_forum_discussion` `d` ON `d`.`forumid` = `f`.`forumid`
                JOIN `somda_forum_posts` `p` ON `p`.`discussionid` = `d`.`discussionid`
                LEFT JOIN `somda_forum_read_' . substr($user->getId(), -1) . '` `r`
                    ON `r`.`uid` = ' . $user->getId() . ' AND `r`.`postid` = `p`.`postid`
                GROUP BY `f`.`forumid`';
        }

        $connection = $this->getEntityManager()->getConnection();
        try {
            $statement = $connection->prepare($query);
            $statement->execute();
            return $statement->fetchAll();
        } catch (DBALException $exception) {
            return [];
        }
    }
}
