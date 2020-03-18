<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaForumFavorites
 *
 * @ORM\Table(name="somda_forum_favorites")
 * @ORM\Entity
 */
class SomdaForumFavorites
{
    /**
     * @var int
     *
     * @ORM\Column(name="discussionid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $discussionid;

    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $uid;

    /**
     * @var int
     *
     * @ORM\Column(name="alerting", type="bigint", nullable=false)
     */
    private $alerting = '0';


}
