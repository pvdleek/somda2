<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaForumPostsText
 *
 * @ORM\Table(name="somda_forum_posts_text")
 * @ORM\Entity
 */
class SomdaForumPostsText
{
    /**
     * @var int
     *
     * @ORM\Column(name="postid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $postid;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     */
    private $text;


}
