<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaGroups
 *
 * @ORM\Table(name="somda_groups")
 * @ORM\Entity
 */
class SomdaGroups
{
    /**
     * @var int
     *
     * @ORM\Column(name="groupid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $groupid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=15, nullable=false)
     */
    private $name = '';


}
