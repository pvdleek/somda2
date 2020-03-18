<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_tdr_treinnummerlijst", indexes={@ORM\Index(name="idx_48381_nr_start", columns={"nr_start"}), @ORM\Index(name="idx_48381_nr_eind", columns={"nr_eind"})})
 * @ORM\Entity
 */
class RouteList
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="nr_start", type="bigint", nullable=false)
     */
    private $firstNumber;

    /**
     * @var int
     * @ORM\Column(name="nr_eind", type="bigint", nullable=false)
     */
    private $lastNumber;

    /**
     * @var TrainTable
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="tdr_nr", referencedColumnName="tdr_nr")
     */
    private $trainTableYear;

    /**
     * @var Transporter
     * @ORM\ManyToOne(targetEntity="App\Entity\Transporter")
     * @ORM\JoinColumn(name="vervoerder_id", referencedColumnName="vervoerder_id")
     */
    private $transporter;

    /**
     * @var Characteristic
     * @ORM\ManyToOne(targetEntity="App\Entity\Characteristic")
     * @ORM\JoinColumn(name="karakteristiek_id", referencedColumnName="karakteristiek_id")
     */
    private $characteristic;

    /**
     * @var string|null
     * @ORM\Column(name="traject", type="string", length=75, nullable=true)
     */
    private $section;

    /**
     * @var Route[]
     * @ORM\ManyToMany(targetEntity="App\Entity\Route", inversedBy="routeLists")
     * @ORM\JoinTable(name="somda_tdr_trein_treinnummerlijst",
     *     joinColumns={@ORM\JoinColumn(name="treinnummerlijst_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="treinid", referencedColumnName="treinid")}
     * )
     */
    private $routes;
}
