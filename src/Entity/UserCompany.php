<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="usc_user_company")
 * @ORM\Entity
 */
class UserCompany
{
    /**
     * @var int|null
     * @ORM\Column(name="usc_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="usc_name", type="string", length=15, nullable=false)
     */
    public string $name;
}
