<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mat_naam")
 * @ORM\Entity
 */
class TrainNameRange
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
    private $start;

    /**
     * @var int
     * @ORM\Column(name="nr_eind", type="bigint", nullable=false)
     */
    private $end;

    /**
     * @var string
     * @ORM\Column(name="naam", type="string", length=25, nullable=false)
     */
    private $name;

    /**
     * @var Transporter
     * @ORM\ManyToOne(targetEntity="App\Entity\Transporter")
     * @ORM\JoinColumn(name="vervoerder_id", referencedColumnName="vervoerder_id")
     */
    private $transporter;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TrainNameRange
     */
    public function setId(int $id): TrainNameRange
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * @param int $start
     * @return TrainNameRange
     */
    public function setStart(int $start): TrainNameRange
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @return int
     */
    public function getEnd(): int
    {
        return $this->end;
    }

    /**
     * @param int $end
     * @return TrainNameRange
     */
    public function setEnd(int $end): TrainNameRange
    {
        $this->end = $end;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return TrainNameRange
     */
    public function setName(string $name): TrainNameRange
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Transporter
     */
    public function getTransporter(): Transporter
    {
        return $this->transporter;
    }

    /**
     * @param Transporter $transporter
     * @return TrainNameRange
     */
    public function setTransporter(Transporter $transporter): TrainNameRange
    {
        $this->transporter = $transporter;
        return $this;
    }
}
