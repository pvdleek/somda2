<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_cats")
 * @ORM\Entity
 */
class ForumCategory
{
    /**
     * @var int
     * @ORM\Column(name="catid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=30, nullable=false)
     */
    private $name;

    /**
     * @var int
     * @ORM\Column(name="volgorde", type="bigint", nullable=false)
     */
    private $order;

    /**
     * @var ForumForum[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumForum", mappedBy="category")
     */
    private $forums;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ForumCategory
     */
    public function setId(int $id): ForumCategory
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ForumCategory
     */
    public function setName(string $name): ForumCategory
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return ForumCategory
     */
    public function setOrder(int $order): ForumCategory
    {
        $this->order = $order;
        return $this;
    }
}
