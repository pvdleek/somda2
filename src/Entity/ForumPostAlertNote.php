<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_alerts_notes", indexes={@ORM\Index(name="idx_47898_alertid", columns={"alertid"})})
 * @ORM\Entity
 */
class ForumPostAlertNote
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPostAlert", inversedBy="notes")
     * @ORM\JoinColumn(name="alertid", referencedColumnName="id")
     */
    public ?ForumPostAlert $alert = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="authorid", referencedColumnName="uid")
     */
    public ?User $author = null;

    /**
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    public ?\DateTime $timestamp = null;

    /**
     * @ORM\Column(name="sent_to_reporter", type="boolean", nullable=false)
     */
    public bool $sentToReporter = false;

    /**
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     */
    public string $text = '';
}
