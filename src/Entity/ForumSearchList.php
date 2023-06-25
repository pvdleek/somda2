<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_zoeken_lijst")
 * @ORM\Entity
 */
class ForumSearchList
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumSearchWord", inversedBy="lists")
     * @ORM\JoinColumn(name="woord_id", referencedColumnName="woord_id")
     * @ORM\Id
     */
    public ?ForumSearchWord $word = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost", inversedBy="searchLists")
     * @ORM\JoinColumn(name="postid", referencedColumnName="postid")
     * @ORM\Id
     */
    public ?ForumPost $post = null;

    /**
     * @ORM\Column(name="titel", type="boolean", nullable=false)
     */
    public bool $title = false;
}
