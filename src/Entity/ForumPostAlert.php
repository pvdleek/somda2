<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ForumPostAlertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForumPostAlertRepository::class)]
#[ORM\Table(name: 'somda_forum_alerts', indexes: [new ORM\Index(name: 'idx_somda_forum_alerts__postid', columns: ['postid'])])]
class ForumPostAlert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ForumPost::class, inversedBy: 'alerts')]
    #[ORM\JoinColumn(name: 'postid', referencedColumnName: 'postid')]
    public ?ForumPost $post = null;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    public bool $closed = false;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'senderid', referencedColumnName: 'uid')]
    public ?User $sender = null;

    #[ORM\Column(nullable: true)]
    public ?\DateTime $timestamp = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $comment = null;

    #[ORM\OneToMany(targetEntity: ForumPostAlertNote::class, mappedBy: 'alert')]
    private Collection $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    /**
     * @param ForumPostAlertNote $forumPostAlertNote
     * @return ForumPostAlert
     */
    public function addNote(ForumPostAlertNote $forumPostAlertNote): ForumPostAlert
    {
        $this->notes[] = $forumPostAlertNote;
        return $this;
    }

    /**
     * @return ForumPostAlertNote[]
     */
    public function getNotes(): array
    {
        return $this->notes->toArray();
    }
}
