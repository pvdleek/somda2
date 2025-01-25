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
     * @ORM\Column(name="groupid", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="name", type="string", length=15, nullable=false)
     */
    public string $name = '';

    /**
     * @ORM\Column(name="roles", type="array", nullable=false)
     */
    public array $roles = [];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="groups")
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
