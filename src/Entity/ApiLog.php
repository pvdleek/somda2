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
    private int $id;

    /**
     * @var string|null
     * @ORM\Column(name="station", type="string", length=10, nullable=true)
     */
    public ?string $station;

    /**
     * @var string|null
     * @ORM\Column(name="tijd", type="string", length=5, nullable=true)
     */
    public ?string $time;

    /**
     * @var int|null
     * @ORM\Column(name="dagnr", type="bigint", nullable=true)
     */
    public ?int $dayNumber;

    /**
     * @var int|null
     * @ORM\Column(name="resultaat_id", type="bigint", nullable=true)
     */
    public ?int $resultId;

    /**
     * @var int|null
     * @ORM\Column(name="datumtijd", type="bigint", nullable=true)
     */
    public ?int $dateTime;

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
     * @return int|null
     */
    public function getDayNumber(): ?int
    {
        return $this->dayNumber;
    }
}
