<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaBlokken
 *
 * @ORM\Table(name="somda_blokken")
 * @ORM\Entity
 */
class SomdaBlokken
{
    /**
     * @var int
     *
     * @ORM\Column(name="blokid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $blokid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=55, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=45, nullable=false)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="url_short", type="string", length=20, nullable=false)
     */
    private $urlShort;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="bigint", nullable=false)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="menu_parent", type="bigint", nullable=false)
     */
    private $menuParent;

    /**
     * @var int
     *
     * @ORM\Column(name="menu_volgorde", type="bigint", nullable=false)
     */
    private $menuVolgorde;

    /**
     * @var int
     *
     * @ORM\Column(name="do_seperator", type="bigint", nullable=false)
     */
    private $doSeperator = '0';

    /**
     * @var int|null
     *
     * @ORM\Column(name="sitemap_last_update", type="bigint", nullable=true)
     */
    private $sitemapLastUpdate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sitemap_frequency", type="string", length=10, nullable=true)
     */
    private $sitemapFrequency;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sitemap_prio", type="bigint", nullable=true)
     */
    private $sitemapPrio;


}
