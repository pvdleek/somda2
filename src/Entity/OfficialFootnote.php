<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(
 *     name="ofo_official_footnote",
 *     indexes={@ORM\Index(name="idx_ofo__footnote_id", columns={"ofo_footnote_id"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="unq_ofo__footnote_id_date", columns={"ofo_footnote_id", "ofo_date"})}
 * )
 * @ORM\Entity
 */
class OfficialFootnote
{
    /**
     * @ORM\Column(name="ofo_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="ofo_footnote_id", type="smallint", nullable=false, options={"unsigned"=true})
     * @JMS\Expose()
     * @OA\Property(description="Unique footnote identifier", type="integer")
     */
    public int $footnoteId = 0;

    /**
     * @ORM\Column(name="ofo_date", type="date", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the date on which the route runs (Y-m-dTH:i:sP)", type="string")
     */
    public ?\DateTime $date = null;
}
