<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaForumAlerts
 *
 * @ORM\Table(name="somda_forum_alerts", indexes={@ORM\Index(name="idx_47886_postid", columns={"postid"})})
 * @ORM\Entity
 */
class SomdaForumAlerts
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="postid", type="bigint", nullable=false)
     */
    private $postid;

    /**
     * @var int
     *
     * @ORM\Column(name="closed", type="bigint", nullable=false)
     */
    private $closed = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="senderid", type="bigint", nullable=false)
     */
    private $senderid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="time", nullable=false)
     */
    private $time;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", length=0, nullable=true)
     */
    private $comment;


}
