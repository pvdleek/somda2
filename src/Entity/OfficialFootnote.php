<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(
 *     name="ofo_official_footnote",
 *     indexes={@ORM\Index(name="IDX_ofo_footnote_id", columns={"ofo_footnote_id"})},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="UNQ_ofo_footnote_id_date", columns={"ofo_footnote_id", "ofo_date"})
 *     }
 * )
 * @ORM\Entity
 */
class OfficialFootnote
{
    /**
     * @var int|null
     * @ORM\Column(name="ofo_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var int
     * @ORM\Column(name="ofo_footnote_id", type="bigint", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Unique footnote identifier", type="integer")
     */
    public int $footnoteId = 0;

    /**
     * @var DateTime
     * @ORM\Column(name="ofo_date", type="date", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="ISO-8601 timestamp of the date on which the route runs (Y-m-dTH:i:sP)", type="string")
     */
    public DateTime $date;
}
