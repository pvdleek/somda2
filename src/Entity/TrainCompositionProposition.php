<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mat_changes")
 * @ORM\Entity
 */
class TrainCompositionProposition
{
    /**
     * @var TrainComposition
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainComposition")
     * @ORM\JoinColumn(name="matsmsid", referencedColumnName="matsmsid")
     * @ORM\Id
     */
    public $composition;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     */
    public $user;

    /**
     * @var DateTime
     * @ORM\Column(name="datum", type="datetime", nullable=false)
     */
    public $timestamp;

    /**
     * @var string|null
     * @ORM\Column(name="bak1", type="string", length=15, nullable=true)
     */
    public $car1;

    /**
     * @var string|null
     * @ORM\Column(name="bak2", type="string", length=15, nullable=true)
     */
    public $car2;

    /**
     * @var string|null
     * @ORM\Column(name="bak3", type="string", length=15, nullable=true)
     */
    public $car3;

    /**
     * @var string|null
     * @ORM\Column(name="bak4", type="string", length=15, nullable=true)
     */
    public $car4;

    /**
     * @var string|null
     * @ORM\Column(name="bak5", type="string", length=15, nullable=true)
     */
    public $car5;

    /**
     * @var string|null
     * @ORM\Column(name="bak6", type="string", length=15, nullable=true)
     */
    public $car6;

    /**
     * @var string|null
     * @ORM\Column(name="bak7", type="string", length=15, nullable=true)
     */
    public $car7;

    /**
     * @var string|null
     * @ORM\Column(name="bak8", type="string", length=15, nullable=true)
     */
    public $car8;

    /**
     * @var string|null
     * @ORM\Column(name="bak9", type="string", length=15, nullable=true)
     */
    public $car9;

    /**
     * @var string|null
     * @ORM\Column(name="bak10", type="string", length=15, nullable=true)
     */
    public $car10;

    /**
     * @var string|null
     * @ORM\Column(name="bak11", type="string", length=15, nullable=true)
     */
    public $car11;

    /**
     * @var string|null
     * @ORM\Column(name="bak12", type="string", length=15, nullable=true)
     */
    public $car12;

    /**
     * @var string|null
     * @ORM\Column(name="bak13", type="string", length=15, nullable=true)
     */
    public $car13;

    /**
     * @var string|null
     * @ORM\Column(name="opmerkingen", type="string", length=255, nullable=true)
     */
    public $note;
}
