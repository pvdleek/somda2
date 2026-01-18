<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'ofo_official_footnote')]
#[ORM\Index(name: 'idx_ofo__footnote_id', columns: ['ofo_footnote_id'])]
#[ORM\UniqueConstraint(name: 'unq_ofo__footnote_id_date', columns: ['ofo_footnote_id', 'ofo_date'])]
class OfficialFootnote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ofo_id', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'ofo_footnote_id', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public int $footnote_id = 0;

    #[ORM\Column(name: 'ofo_date', type: 'date', nullable: true)]
    public ?\DateTime $date = null;
}
