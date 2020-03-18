<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaPoll
 *
 * @ORM\Table(name="somda_poll", indexes={@ORM\Index(name="idx_48191_date", columns={"date"})})
 * @ORM\Entity
 */
class SomdaPoll
{
    /**
     * @var int
     *
     * @ORM\Column(name="pollid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pollid;

    /**
     * @var string
     *
     * @ORM\Column(name="question", type="string", length=200, nullable=false)
     */
    private $question = '';

    /**
     * @var string
     *
     * @ORM\Column(name="opt_a", type="string", length=150, nullable=false)
     */
    private $optA = '';

    /**
     * @var string
     *
     * @ORM\Column(name="opt_b", type="string", length=150, nullable=false)
     */
    private $optB = '';

    /**
     * @var string
     *
     * @ORM\Column(name="opt_c", type="string", length=150, nullable=false)
     */
    private $optC = '';

    /**
     * @var string
     *
     * @ORM\Column(name="opt_d", type="string", length=150, nullable=false)
     */
    private $optD = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;


}
