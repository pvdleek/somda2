<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fpn_forum_post_alert_note", indexes={
 *     @ORM\Index(name="IDX_fpn_fpa_id", columns={"fpn_fpa_id"}),
 *     @ORM\Index(name="IDX_fpn_author_use_id", columns={"fpn_author_use_id"})
 * })
 * @ORM\Entity
 */
class ForumPostAlertNote
{
    /**
     * @var int|null
     * @ORM\Column(name="fpn_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var ForumPostAlert
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPostAlert", inversedBy="notes")
     * @ORM\JoinColumn(name="fpn_fpa_id", referencedColumnName="fpa_id")
     */
    public ForumPostAlert $alert;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="fpn_author_use_id", referencedColumnName="use_id")
     */
    public User $author;

    /**
     * @var DateTime
     * @ORM\Column(name="fpn_timestamp", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var bool
     * @ORM\Column(name="fpn_sent_to_reporter", type="boolean", nullable=false)
     */
    public bool $sentToReporter;

    /**
     * @var string
     * @ORM\Column(name="fpn_text", type="text", length=0, nullable=false)
     */
    public string $text;
}
