<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_tdr_drgl")
 * @ORM\Entity
 */
class TrainTableYear
{
    /**
     * @var int
     * @ORM\Column(name="tdr_nr", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="naam", type="string", length=10, nullable=false)
     */
    private $name;

    /**
     * @var DateTime
     * @ORM\Column(name="start_datum", type="date", nullable=false)
     */
    private $startDate;

    /**
     * @var DateTime
     * @ORM\Column(name="eind_datum", type="date", nullable=false)
     */
    private $endDate;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TrainTableYear
     */
    public function setId(int $id): TrainTableYear
    {
        $this->id = $id;
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
     * @return TrainTableYear
     */
    public function setName(string $name): TrainTableYear
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @param DateTime $startDate
     * @return TrainTableYear
     */
    public function setStartDate(DateTime $startDate): TrainTableYear
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    /**
     * @param DateTime $endDate
     * @return TrainTableYear
     */
    public function setEndDate(DateTime $endDate): TrainTableYear
    {
        $this->endDate = $endDate;
        return $this;
    }
}
