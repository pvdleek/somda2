<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_drgl")
 * @ORM\Entity
 */
class SpecialRoute
{
    /**
     * @var int
     * @ORM\Column(name="drglid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     * @ORM\Column(name="werkzaamheden", type="boolean", nullable=false)
     */
    private $construction = false;

    /**
     * @var DateTime|null
     * @ORM\Column(name="pubdatum", type="datetime", nullable=true)
     */
    private $publicationTimestamp;

    /**
     * @var DateTime
     * @ORM\Column(name="datum", type="date", nullable=false)
     */
    private $startDate;

    /**
     * @var DateTime|null
     * @ORM\Column(name="einddatum", type="date", nullable=true)
     */
    private $endDate;

    /**
     * @var boolean
     * @ORM\Column(name="public", type="boolean", nullable=false)
     */
    private $public = false;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=75, nullable=false)
     */
    private $title = '';

    /**
     * @var string
     * @ORM\Column(name="image", type="string", length=20, nullable=false)
     */
    private $image = '';

    /**
     * @var string
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     */
    private $text;

    /**
     * @var SpecialRouteLog[]
     * @ORM\OneToMany(targetEntity="App\Entity\SpecialRouteLog", mappedBy="specialRoute")
     */
    private $logs;

    /**
     *
     */
    public function __construct()
    {
        $this->logs = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return SpecialRoute
     */
    public function setId(int $id): SpecialRoute
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return bool
     */
    public function isConstruction(): bool
    {
        return $this->construction;
    }

    /**
     * @param bool $construction
     * @return SpecialRoute
     */
    public function setConstruction(bool $construction): SpecialRoute
    {
        $this->construction = $construction;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getPublicationTimestamp(): ?DateTime
    {
        return $this->publicationTimestamp;
    }

    /**
     * @param DateTime|null $publicationTimestamp
     * @return SpecialRoute
     */
    public function setPublicationTimestamp(?DateTime $publicationTimestamp): SpecialRoute
    {
        $this->publicationTimestamp = $publicationTimestamp;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @param DateTime $startDate
     * @return SpecialRoute
     */
    public function setStartDate(DateTime $startDate): SpecialRoute
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @param DateTime|null $endDate
     * @return SpecialRoute
     */
    public function setEndDate(?DateTime $endDate): SpecialRoute
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->public;
    }

    /**
     * @param bool $public
     * @return SpecialRoute
     */
    public function setPublic(bool $public): SpecialRoute
    {
        $this->public = $public;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return SpecialRoute
     */
    public function setTitle(string $title): SpecialRoute
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     * @return SpecialRoute
     */
    public function setImage(string $image): SpecialRoute
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return SpecialRoute
     */
    public function setText(string $text): SpecialRoute
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param SpecialRouteLog $specialRouteLog
     * @return SpecialRoute
     */
    public function addLog(SpecialRouteLog $specialRouteLog): SpecialRoute
    {
        $this->logs[] = $specialRouteLog;
        return $this;
    }

    /**
     * @return SpecialRouteLog[]
     */
    public function getLogs(): array
    {
        return $this->logs->toArray();
    }
}
