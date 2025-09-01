<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_forum_alerts_notes', indexes: [new ORM\Index(name: 'idx_somda_forum_alerts_notes__alertid', columns: ['alertid'])])]
class ForumPostAlertNote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ForumPostAlert::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(name: 'alertid', referencedColumnName: 'id')]
    public ?ForumPostAlert $alert = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'authorid', referencedColumnName: 'uid')]
    public ?User $author = null;

    #[ORM\Column(nullable: true)]
    public ?\DateTime $timestamp = null;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    public bool $sent_to_reporter = false;

    #[ORM\Column(type: 'text', nullable: false, options: ['default' => ''])]
    public string $text = '';
}
