<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaVervoerder
 *
 * @ORM\Table(name="somda_vervoerder", uniqueConstraints={@ORM\UniqueConstraint(name="idx_49122_prorail_desc", columns={"prorail_desc"}), @ORM\UniqueConstraint(name="idx_49122_omschrijving", columns={"omschrijving"})})
 * @ORM\Entity
 */
class SomdaVervoerder
{
    /**
     * @var int
     *
     * @ORM\Column(name="vervoerder_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $vervoerderId;

    /**
     * @var string
     *
     * @ORM\Column(name="omschrijving", type="string", length=35, nullable=false)
     */
    private $omschrijving = '';

    /**
     * @var string
     *
     * @ORM\Column(name="prorail_desc", type="string", length=35, nullable=false)
     */
    private $prorailDesc = '';


}
