<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_groups")
 * @ORM\Entity
 */
class Group
{
    /**
     * @var int
     * @ORM\Column(name="groupid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=15, nullable=false)
     */
    private $name = '';

    /**
     * @var array
     * @ORM\Column(name="roles", type="array", nullable=false)
     */
    private $roles = [];

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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Group
     */
    public function setId(int $id): Group
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Group
     */
    public function setName(string $name): Group
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return Group
     */
    public function setRoles(array $roles): Group
    {
        $this->roles = $roles;
        return $this;
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
