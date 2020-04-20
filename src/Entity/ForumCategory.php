<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_cats")
 * @ORM\Entity
 */
class ForumCategory extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="catid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=30, nullable=false)
     */
    public $name;

    /**
     * @var int
     * @ORM\Column(name="volgorde", type="bigint", nullable=false)
     */
    public $order;

    /**
     * @var ForumForum[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumForum", mappedBy="category")
     */
    private $forums;

    /**
     *
     */
    public function __construct()
    {
        $this->forums = new ArrayCollection();
    }

    /**
     * @param ForumForum $forum
     * @return ForumCategory
     */
    public function addForum(ForumForum $forum): ForumCategory
    {
        $this->forums[] = $forum;
        return $this;
    }

    /**
     * @return ForumForum[]
     */
    public function getForums(): array
    {
        return $this->forums->toArray();
    }
}
