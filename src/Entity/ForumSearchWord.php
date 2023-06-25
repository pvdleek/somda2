<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="somda_forum_zoeken_woorden",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_48035_woord", columns={"woord"})}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ForumSearchWord")
 */
class ForumSearchWord
{
    /**
     * @ORM\Column(name="woord_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="woord", type="string", length=50, nullable=false)
     */
    public string $word = '';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ForumSearchList", mappedBy="word")
     */
    private $lists;

    public function __construct()
    {
        $this->lists = new ArrayCollection();
    }

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
