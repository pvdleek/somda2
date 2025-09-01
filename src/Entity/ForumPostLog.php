<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'somda_forum_log')]
class ForumPostLog
{
    public const ACTION_POST_NEW = 0;
    public const ACTION_POST_EDIT = 1;
    public const ACTION_VALUES = [self::ACTION_POST_NEW, self::ACTION_POST_EDIT];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ForumPost::class, inversedBy: 'logs')]
    #[ORM\JoinColumn(name: 'postid', referencedColumnName: 'postid')]
    public ?ForumPost $post = null;

    #[ORM\Column(name: 'actie', type: 'smallint', nullable: false, options: ['default' => self::ACTION_POST_NEW, 'unsigned' => true])]
    #[Assert\Choice(choices: ForumPostLog::ACTION_VALUES)]
    public int $action = self::ACTION_POST_NEW;
}
