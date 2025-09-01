<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

#[ORM\Entity]
#[ORM\Table(
    name: 'ofo_official_footnote',
    indexes: [new ORM\Index(name: 'idx_ofo__footnote_id', columns: ['ofo_footnote_id'])],
    uniqueConstraints: [new ORM\UniqueConstraint(name: 'unq_ofo__footnote_id_date', columns: ['ofo_footnote_id', 'ofo_date'])]
)]
class OfficialFootnote
{
    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ofo_id', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique footnote identifier", type="integer")
     */
    #[ORM\Column(name: 'ofo_footnote_id', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public int $footnote_id = 0;

    /**
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the date on which the route runs (Y-m-dTH:i:sP)", type="string")
     */
    #[ORM\Column(name: 'ofo_date', type: 'date', nullable: true)]
    public ?\DateTime $date = null;
}
