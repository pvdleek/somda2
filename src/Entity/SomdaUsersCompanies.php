<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaUsersCompanies
 *
 * @ORM\Table(name="somda_users_companies")
 * @ORM\Entity
 */
class SomdaUsersCompanies
{
    /**
     * @var int
     *
     * @ORM\Column(name="bedrijf_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $bedrijfId;

    /**
     * @var string
     *
     * @ORM\Column(name="naam", type="string", length=15, nullable=false)
     */
    private $naam;


}
