<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(name="tty_train_table_year")
 * @ORM\Entity(repositoryClass="App\Repository\TrainTableYear")
 */
class TrainTableYear
{
    /**
     * @var int|null
     * @ORM\Column(name="tty_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="tty_name", type="string", length=10, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Name of the trainTableYear", maxLength=10, type="string")
     */
    public string $name;

    /**
     * @var DateTime
     * @ORM\Column(name="tty_start_date", type="date", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="The start-date (00:00:00)")
     */
    public DateTime $startDate;

    /**
     * @var DateTime
     * @ORM\Column(name="tty_end_date", type="date", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="The end-date (23:59:59)")
     */
    public DateTime $endDate;

    /**
     * @return bool
     * @throws Exception
     * @JMS\VirtualProperty(name="active")
     * @SWG\Property(description="Indication if the trainTableYear is currently active")
     */
    public function isActive(): bool
    {
        return $this->startDate <= new DateTime() && $this->endDate >= new DateTime();
    }
}
