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
     * @ORM\Column(name="bedrijf_id", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="naam", type="string", length=15, nullable=false)
     */
    public string $name = '';
}
