<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_users_companies')]
class UserCompany
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'bedrijf_id', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'naam', length: 15, nullable: false, options: ['default' => ''])]
    public string $name = '';
}
