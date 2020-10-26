<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="gro_group")
 * @ORM\Entity
 */
class Group
{
    /**
     * @var int|null
     * @ORM\Column(name="gro_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="gro_name", type="string", length=15, nullable=false)
     */
    public string $name = '';

    /**
     * @var array
     * @ORM\Column(name="gro_roles", type="array", nullable=false)
     */
    public array $roles = [];

    /**
     * @var User
     * @ORM\ManyToMany(targetEntity="User", inversedBy="groups")
     * @ORM\JoinTable(name="usg_user_group",
     *      joinColumns={@ORM\JoinColumn(name="usg_gro_id", referencedColumnName="gro_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="usg_use_id", referencedColumnName="use_id")}
     * )
     */
    private $users;

    /**
     *
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * @param User $user
     * @return Group
     */
    public function addUser(User $user): Group
    {
        $this->users[] = $user;
        return $this;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users->toArray();
    }
}
