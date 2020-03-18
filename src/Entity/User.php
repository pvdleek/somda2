<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="somda_users", indexes={@ORM\Index(name="idx_49053_uname", columns={"username"})})
 * @ORM\Entity
 */
class User implements UserInterface
{
    /**
     * @var int
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="active", type="bigint", nullable=false)
     */
    private $active;

    /**
     * @var int
     * @ORM\Column(name="spots_ok", type="bigint", nullable=false)
     */
    private $spotsOk = '0';

    /**
     * @var string
     * @ORM\Column(name="username", type="string", length=10, nullable=false)
     */
    private $username = '';

    /**
     * @var string|null
     * @ORM\Column(name="name", type="string", length=40, nullable=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="password", type="string", length=32, nullable=false)
     */
    private $password = '';

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=60, nullable=false)
     */
    private $email = '';

    /**
     * @var string
     * @ORM\Column(name="cookie_ok", type="string", length=3, nullable=false)
     */
    private $cookieOk = '0';

    /**
     * @var string
     * @ORM\Column(name="actkey", type="string", length=32, nullable=false)
     */
    private $activationKey = '0';

    /**
     * @var DateTime
     * @ORM\Column(name="regdate", type="date", nullable=false)
     */
    private $registrationDate;

    /**
     * @var array
     */
    private $roles = [];

    /**
     * @var Group
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", mappedBy="users")
     */
    private $groups;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return User
     */
    public function setId(int $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getActive(): int
    {
        return $this->active;
    }

    /**
     * @param int $active
     * @return User
     */
    public function setActive(int $active): User
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return int
     */
    public function getSpotsOk(): int
    {
        return $this->spotsOk;
    }

    /**
     * @param int $spotsOk
     * @return User
     */
    public function setSpotsOk(int $spotsOk): User
    {
        $this->spotsOk = $spotsOk;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return User
     */
    public function setName(?string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getCookieOk(): string
    {
        return $this->cookieOk;
    }

    /**
     * @param string $cookieOk
     * @return User
     */
    public function setCookieOk(string $cookieOk): User
    {
        $this->cookieOk = $cookieOk;
        return $this;
    }

    /**
     * @return string
     */
    public function getActivationKey(): string
    {
        return $this->activationKey;
    }

    /**
     * @param string $activationKey
     * @return User
     */
    public function setActivationKey(string $activationKey): User
    {
        $this->activationKey = $activationKey;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRegistrationDate(): DateTime
    {
        return $this->registrationDate;
    }

    /**
     * @param DateTime $registrationDate
     * @return User
     */
    public function setRegistrationDate(DateTime $registrationDate): User
    {
        $this->registrationDate = $registrationDate;
        return $this;
    }

    /**
     * @return Group
     */
    public function getGroups(): Group
    {
        return $this->groups;
    }

    /**
     * @param Group $groups
     * @return User
     */
    public function setGroups(Group $groups): User
    {
        $this->groups = $groups;
        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }
}
