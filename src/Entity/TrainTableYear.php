<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="somda_tdr_drgl")
 * @ORM\Entity(repositoryClass="App\Repository\TrainTableYear")
 */
class TrainTableYear extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="tdr_nr", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     */
    protected ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="naam", type="string", length=10, nullable=false)
     * @JMS\Expose()
     */
    public string $name;

    /**
     * @var DateTime
     * @ORM\Column(name="start_datum", type="date", nullable=false)
     * @JMS\Expose()
     */
    public DateTime $startDate;

    /**
     * @var DateTime
     * @ORM\Column(name="eind_datum", type="date", nullable=false)
     * @JMS\Expose()
     */
    public DateTime $endDate;
}
