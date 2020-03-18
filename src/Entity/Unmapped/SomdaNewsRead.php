<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaNewsRead
 *
 * @ORM\Table(name="somda_news_read")
 * @ORM\Entity
 */
class SomdaNewsRead
{
    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $uid;

    /**
     * @var int
     *
     * @ORM\Column(name="newsid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $newsid;


}
