<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaTrein
 *
 * @ORM\Table(name="somda_trein", uniqueConstraints={@ORM\UniqueConstraint(name="idx_49046_treinnr", columns={"treinnr"})})
 * @ORM\Entity
 */
class SomdaTrein
{
    /**
     * @var int
     *
     * @ORM\Column(name="treinid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $treinid;

    /**
     * @var string
     *
     * @ORM\Column(name="treinnr", type="string", length=15, nullable=false)
     */
    private $treinnr = '';


}
