<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_groups")
 * @ORM\Entity
 */
class Group extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="groupid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=15, nullable=false)
     */
    public string $name = '';

    /**
     * @var array
     * @ORM\Column(name="roles", type="array", nullable=false)
     */
    public array $roles = [];

    /**
     * @var User
     * @ORM\ManyToMany(targetEntity="User", inversedBy="groups")
     * @ORM\JoinTable(name="somda_users_groups",
     *      joinColumns={@ORM\JoinColumn(name="groupid", referencedColumnName="groupid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="uid", referencedColumnName="uid")}
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
