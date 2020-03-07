<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaHelp
 *
 * @ORM\Table(name="somda_help")
 * @ORM\Entity
 */
class SomdaHelp
{
    /**
     * @var int
     *
     * @ORM\Column(name="contentid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $contentid;

    /**
     * @var string
     *
     * @ORM\Column(name="titel", type="string", length=35, nullable=false)
     */
    private $titel = '';

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=20, nullable=false)
     */
    private $url = '';

    /**
     * @var int
     *
     * @ORM\Column(name="authorid", type="bigint", nullable=false)
     */
    private $authorid;


}
