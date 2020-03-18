<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_discussion", indexes={@ORM\Index(name="idx_47915_forumid", columns={"forumid"})})
 * @ORM\Entity(repositoryClass="App\Repository\ForumDiscussion")
 */
class ForumDiscussion
{
    /**
     * @var int
     * @ORM\Column(name="discussionid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="disc_type", type="bigint", nullable=false)
     */
    private $type = 0;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=50, nullable=false)
     */
    private $title = '';

    /**
     * @var int
     * @ORM\Column(name="viewed", type="bigint", nullable=false)
     */
    private $viewed = 0;

    /**
     * @var boolean
     * @ORM\Column(name="locked", type="boolean", nullable=false)
     */
    private $locked = false;

    /**
     * @var ForumForum
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumForum", inversedBy="discussions")
     * @ORM\JoinColumn(name="forumid", referencedColumnName="forumid")
     */
    private $forum;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="authorid", referencedColumnName="uid")
     */
    private $author;

    private $posts;



}
