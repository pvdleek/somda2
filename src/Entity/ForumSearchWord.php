<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_zoeken_woorden", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48035_woord", columns={"woord"})})
 * @ORM\Entity
 */
class ForumSearchWord
{
    /**
     * @var int
     * @ORM\Column(name="woord_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="woord", type="string", length=20, nullable=false)
     */
    private $word;

    /**
     * @var ForumSearchList[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumSearchList", mappedBy="word")
     */
    private $lists;

    /**
     *
     */
    public function __construct()
    {
        $this->lists = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ForumSearchWord
     */
    public function setId(int $id): ForumSearchWord
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getWord(): string
    {
        return $this->word;
    }

    /**
     * @param string $word
     * @return ForumSearchWord
     */
    public function setWord(string $word): ForumSearchWord
    {
        $this->word = $word;
        return $this;
    }

    /**
     * @param ForumSearchList $forumSearchList
     * @return ForumSearchWord
     */
    public function addList(ForumSearchList $forumSearchList): ForumSearchWord
    {
        $this->lists[] = $forumSearchList;
        return $this;
    }

    /**
     * @return ForumSearchList[]
     */
    public function getLists(): array
    {
        return $this->lists->toArray();
    }
}
