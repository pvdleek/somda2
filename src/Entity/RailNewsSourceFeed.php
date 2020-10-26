<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="rnf_rail_news_source_feed", indexes={@ORM\Index(name="IDX_rnf_rns_id", columns={"rnf_rns_id"})})
 * @ORM\Entity
 */
class RailNewsSourceFeed
{
    /**
     * @var int|null
     * @ORM\Column(name="rnf_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="rnf_url", type="string", length=255, nullable=false)
     */
    public string $url;

    /**
     * @var bool
     * @ORM\Column(name="rnf_filter_results", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $filterResults;

    /**
     * @var RailNewsSource
     * @ORM\ManyToOne(targetEntity="App\Entity\RailNewsSource", inversedBy="feeds")
     * @ORM\JoinColumn(name="rnf_rns_id", referencedColumnName="rns_id")
     */
    public RailNewsSource $source;
}
