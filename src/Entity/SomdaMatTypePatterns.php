<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaMatTypePatterns
 *
 * @ORM\Table(name="somda_mat_type_patterns", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48162_mattype", columns={"mattype"})})
 * @ORM\Entity
 */
class SomdaMatTypePatterns
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="mattype", type="string", length=6, nullable=false)
     */
    private $mattype;

    /**
     * @var string
     *
     * @ORM\Column(name="omschrijving", type="string", length=32, nullable=false)
     */
    private $omschrijving;

    /**
     * @var string
     *
     * @ORM\Column(name="pattern_id_list", type="string", length=100, nullable=false)
     */
    private $patternIdList;


}
