<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_users_companies")
 * @ORM\Entity
 */
class UserCompany
{
    /**
     * @var int|null
     * @ORM\Column(name="bedrijf_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="naam", type="string", length=15, nullable=false)
     */
    public string $name;
}
