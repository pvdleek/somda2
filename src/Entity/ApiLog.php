<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_api_logging")
 * @ORM\Entity
 */
class ApiLog
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(name="station", type="string", length=10, nullable=true)
     */
    private $station;

    /**
     * @var string|null
     * @ORM\Column(name="tijd", type="string", length=5, nullable=true)
     */
    private $time;

    /**
     * @var int|null
     * @ORM\Column(name="dagnr", type="bigint", nullable=true)
     */
    private $dayNumber;

    /**
     * @var int|null
     * @ORM\Column(name="resultaat_id", type="bigint", nullable=true)
     */
    private $resultId;

    /**
     * @var int|null
     * @ORM\Column(name="datumtijd", type="bigint", nullable=true)
     */
    private $dateTime;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ApiLog
     */
    public function setId(int $id): ApiLog
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStation(): ?string
    {
        return $this->station;
    }

    /**
     * @param string|null $station
     * @return ApiLog
     */
    public function setStation(?string $station): ApiLog
    {
        $this->station = $station;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTime(): ?string
    {
        return $this->time;
    }

    /**
     * @param string|null $time
     * @return ApiLog
     */
    public function setTime(?string $time): ApiLog
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDayNumber(): ?int
    {
        return $this->dayNumber;
    }

    /**
     * @param int|null $dayNumber
     * @return ApiLog
     */
    public function setDayNumber(?int $dayNumber): ApiLog
    {
        $this->dayNumber = $dayNumber;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getResultId(): ?int
    {
        return $this->resultId;
    }

    /**
     * @param int|null $resultId
     * @return ApiLog
     */
    public function setResultId(?int $resultId): ApiLog
    {
        $this->resultId = $resultId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDateTime(): ?int
    {
        return $this->dateTime;
    }

    /**
     * @param int|null $dateTime
     * @return ApiLog
     */
    public function setDateTime(?int $dateTime): ApiLog
    {
        $this->dateTime = $dateTime;
        return $this;
    }
}
