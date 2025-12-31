<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_users_prefs')]
class UserPreferenceValue
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'preferences')]
    #[ORM\JoinColumn(name: 'uid', referencedColumnName: 'uid')]
    public ?User $user = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: UserPreference::class)]
    #[ORM\JoinColumn(name: 'prefid', referencedColumnName: 'prefid')]
    public ?UserPreference $preference = null;

    #[ORM\Column(length: 200, nullable: false, options: ['default' => ''])]
    public string $value = '';
}
