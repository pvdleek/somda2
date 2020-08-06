<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(
 *     name="ofo_official_footnote",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_ofo_footnote", columns={"ofo_footnote_id", "ofo_date"})}
 * )
 * @ORM\Entity
 */
class OfficialFootnote extends Entity
{
    /**
     * @var int|null
     * @ORM\Column(name="ofo_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    protected ?int $id = null;

    /**
     * @var int
     * @ORM\Column(name="ofo_footnote_id", type="bigint", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Unique footnote identifier", type="integer")
     */
    public int $footnoteId = 0;

    /**
     * @var DateTime
     * @ORM\Column(name="ofo_date", type="datetime", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="A date on which the route runs", type="date")
     */
    public DateTime $date;
}
