<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="fsw_forum_search_word",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="UNQ_fsw_word", columns={"fsw_word"})}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ForumSearchWord")
 */
class ForumSearchWord
{
    /**
     * @var int|null
     * @ORM\Column(name="fsw_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="fsw_word", type="string", length=50, nullable=false)
     */
    public string $word;

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
