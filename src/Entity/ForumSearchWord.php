<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ForumSearchWordRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForumSearchWordRepository::class)]
#[ORM\Table(name: 'somda_forum_zoeken_woorden')]
#[ORM\UniqueConstraint(name: 'unq_somda_forum_zoeken_woorden__woord', columns: ['woord'])]
class ForumSearchWord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'woord_id', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'woord', length: 50, nullable: false, options: ['default' => ''])]
    public string $word = '';

    #[ORM\OneToMany(targetEntity: ForumSearchList::class, mappedBy: 'word')]
    private Collection $lists;

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
