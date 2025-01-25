<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_drgl")
 * @ORM\Entity(repositoryClass="App\Repository\SpecialRoute")
 */
class SpecialRoute
{
    /**
     * @ORM\Column(name="drglid", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="pubdatum", type="datetime", nullable=true)
     */
    public ?\DateTime $publicationTimestamp = null;

    /**
     * @ORM\Column(name="datum", type="date", nullable=false)
     */
    public ?\DateTime $startDate = null;

    /**
     * @ORM\Column(name="einddatum", type="date", nullable=true)
     */
    public ?\DateTime $endDate = null;

    /**
     * @ORM\Column(name="public", type="boolean", nullable=false)
     */
    public bool $public = false;

    /**
     * @ORM\Column(name="title", type="string", length=75, nullable=false)
     */
    public string $title = '';

    /**
     * @ORM\Column(name="image", type="string", length=20, nullable=false)
     */
    public string $image = '';

    /**
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     */
    public string $text = '';

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     * @ORM\JoinTable(name="somda_drgl_read",
     *      joinColumns={@ORM\JoinColumn(name="drglid", referencedColumnName="drglid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="uid", referencedColumnName="uid")}
     * )
     */
    private $userReads;

    public function __construct()
    {
        $this->userReads = new ArrayCollection();
    }

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
