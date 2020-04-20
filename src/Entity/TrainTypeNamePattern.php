<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mat_type_patterns", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48162_mattype", columns={"mattype"})})
 * @ORM\Entity
 */
class TrainTypeNamePattern extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="mattype", type="string", length=6, nullable=false)
     */
    public $name;

    /**
     * @var string
     * @ORM\Column(name="omschrijving", type="string", length=32, nullable=false)
     */
    public $description;

    /**
     * @var string
     * @ORM\Column(name="pattern_id_list", type="string", length=100, nullable=false)
     */
    public $patternIdList;
}
