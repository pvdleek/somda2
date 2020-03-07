<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaForumLog
 *
 * @ORM\Table(name="somda_forum_log")
 * @ORM\Entity
 */
class SomdaForumLog
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="postid", type="bigint", nullable=false)
     */
    private $postid;

    /**
     * @var int
     *
     * @ORM\Column(name="actie", type="bigint", nullable=false)
     */
    private $actie;


}
