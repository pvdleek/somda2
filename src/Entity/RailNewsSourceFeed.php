<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_snf_spoor_nieuws_bron_feed")
 * @ORM\Entity
 */
class RailNewsSourceFeed
{
    /**
     * @ORM\Column(name="snf_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="snf_url", type="string", length=255, nullable=false)
     */
    public string $url = '';

    /**
     * @ORM\Column(name="snf_filter_results", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $filterResults = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RailNewsSource", inversedBy="feeds")
     * @ORM\JoinColumn(name="snf_snb_id", referencedColumnName="snb_id")
     */
    public ?RailNewsSource $source = null;
}
