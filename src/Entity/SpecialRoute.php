<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_drgl")
 * @ORM\Entity(repositoryClass="App\Repository\SpecialRoute")
 */
class SpecialRoute extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="drglid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var boolean
     * @ORM\Column(name="werkzaamheden", type="boolean", nullable=false)
     */
    public $construction = false;

    /**
     * @var DateTime|null
     * @ORM\Column(name="pubdatum", type="datetime", nullable=true)
     */
    public $publicationTimestamp;

    /**
     * @var DateTime
     * @ORM\Column(name="datum", type="date", nullable=false)
     */
    public $startDate;

    /**
     * @var DateTime|null
     * @ORM\Column(name="einddatum", type="date", nullable=true)
     */
    public $endDate;

    /**
     * @var boolean
     * @ORM\Column(name="public", type="boolean", nullable=false)
     */
    public $public = false;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=75, nullable=false)
     */
    public $title = '';

    /**
     * @var string
     * @ORM\Column(name="image", type="string", length=20, nullable=false)
     */
    public $image = '';

    /**
     * @var string
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     */
    public $text;

    /**
     * @var User[]
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="somda_drgl_read",
     *      joinColumns={@ORM\JoinColumn(name="drglid", referencedColumnName="drglid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="uid", referencedColumnName="uid")}
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
