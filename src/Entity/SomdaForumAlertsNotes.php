<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaForumAlertsNotes
 *
 * @ORM\Table(name="somda_forum_alerts_notes", indexes={@ORM\Index(name="idx_47898_alertid", columns={"alertid"})})
 * @ORM\Entity
 */
class SomdaForumAlertsNotes
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
     * @ORM\Column(name="alertid", type="bigint", nullable=false)
     */
    private $alertid;

    /**
     * @var int
     *
     * @ORM\Column(name="authorid", type="bigint", nullable=false)
     */
    private $authorid;

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
     * @var int
     *
     * @ORM\Column(name="sent_to_reporter", type="bigint", nullable=false)
     */
    private $sentToReporter;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     */
    private $text;


}
