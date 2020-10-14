<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="spr_special_route")
 * @ORM\Entity(repositoryClass="App\Repository\SpecialRoute")
 */
class SpecialRoute
{
    /**
     * @var int|null
     * @ORM\Column(name="spr_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var bool
     * @ORM\Column(name="spr_construction", type="boolean", nullable=false)
     */
    public bool $construction = false;

    /**
     * @var DateTime|null
     * @ORM\Column(name="spr_publication_timestamp", type="datetime", nullable=true)
     */
    public ?DateTime $publicationTimestamp;

    /**
     * @var DateTime
     * @ORM\Column(name="spr_start_date", type="date", nullable=false)
     */
    public DateTime $startDate;

    /**
     * @var DateTime|null
     * @ORM\Column(name="spr_end_date", type="date", nullable=true)
     */
    public ?DateTime $endDate = null;

    /**
     * @var bool
     * @ORM\Column(name="spr_public", type="boolean", nullable=false)
     */
    public bool $public = false;

    /**
     * @var string
     * @ORM\Column(name="spr_title", type="string", length=75, nullable=false)
     */
    public string $title = '';

    /**
     * @var string
     * @ORM\Column(name="spr_image", type="string", length=20, nullable=false)
     */
    public string $image = '';

    /**
     * @var string
     * @ORM\Column(name="spr_text", type="text", length=0, nullable=false)
     */
    public string $text = '';

    /**
     * @var User[]
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="srr_special_route_read",
     *      joinColumns={@ORM\JoinColumn(name="srr_spr_id", referencedColumnName="spr_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="srr_use_id", referencedColumnName="use_id")}
     * )
     */
    private $userReads;

    /**
     *
     */
    public function __construct()
    {
        $this->userReads = new ArrayCollection();
    }

    /**
     * @param User $user
     * @return SpecialRoute
     */
    public function addUserRead(User $user): SpecialRoute
    {
        $this->userReads[] = $user;
        return $this;
    }

    /**
     * @return User[]
     */
    public function getUserReads(): array
    {
        return $this->userReads->toArray();
    }
}
