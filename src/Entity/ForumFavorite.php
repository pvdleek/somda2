<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'somda_forum_favorites')]
class ForumFavorite
{
    public const ALERTING_OFF = 0;
    public const ALERTING_ON = 1;
    public const ALERTING_SENT = 2;
    public const ALERTING_VALUES = [self::ALERTING_OFF, self::ALERTING_ON, self::ALERTING_SENT];

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ForumDiscussion::class, inversedBy: 'favorites')]
    #[ORM\JoinColumn(name: 'discussionid', referencedColumnName: 'discussionid')]
    public ?ForumDiscussion $discussion = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'forum_favorites')]
    #[ORM\JoinColumn(name: 'uid', referencedColumnName: 'uid')]
    public ?User $user = null;

    #[ORM\Column(name: 'alerting', type: 'smallint', nullable: false, options: ['default' => self::ALERTING_OFF, 'unsigned' => true])]
    #[Assert\Choice(choices: self::ALERTING_VALUES)]
    public int $alerting = self::ALERTING_OFF;
}
