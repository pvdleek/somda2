<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaVerkCats
 *
 * @ORM\Table(name="somda_verk_cats")
 * @ORM\Entity
 */
class SomdaVerkCats
{
    /**
     * @var int
     *
     * @ORM\Column(name="verk_catid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $verkCatid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    private $name = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="ns_code", type="string", length=2, nullable=true)
     */
    private $nsCode;


}
