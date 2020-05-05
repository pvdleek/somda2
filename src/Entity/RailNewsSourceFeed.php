<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_snf_spoor_nieuws_bron_feed")
 * @ORM\Entity
 */
class RailNewsSourceFeed extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="snf_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="snf_url", type="string", length=255, nullable=false)
     */
    public string $url;

    /**
     * @var bool
     * @ORM\Column(name="snf_filter_results", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $filterResults;

    /**
     * @var RailNewsSource
     * @ORM\ManyToOne(targetEntity="App\Entity\RailNewsSource", inversedBy="feeds")
     * @ORM\JoinColumn(name="snf_snb_id", referencedColumnName="snb_id")
     */
    public RailNewsSource $source;
}
