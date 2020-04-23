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
     * @var ForumSearchWord
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumSearchWord", inversedBy="lists")
     * @ORM\JoinColumn(name="woord_id", referencedColumnName="woord_id")
     * @ORM\Id
     */
    public ForumSearchWord $word;

    /**
     * @var ForumPost
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost", inversedBy="searchLists")
     * @ORM\JoinColumn(name="postid", referencedColumnName="postid")
     * @ORM\Id
     */
    public ForumPost $post;

    /**
     * @var bool
     * @ORM\Column(name="titel", type="boolean", nullable=false)
     */
    public bool $title = false;
}
