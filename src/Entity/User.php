<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="somda_users", indexes={@ORM\Index(name="idx_49053_uname", columns={"username"})})
 * @ORM\Entity(repositoryClass="App\Repository\User")
 */
class User implements UserInterface
{
    public const COOKIE_NOT_OK = 'nok';
    public const COOKIE_OK = 'ok';
    public const COOKIE_UNKNOWN = '0';

    /**
     * @var int
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = false;

    /**
     * @var int
     * @ORM\Column(name="spots_ok", type="integer", nullable=false)
     */
    private $spotsOk = 0;

    /**
     * @var string
     * @ORM\Column(name="username", type="string", length=10, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 2,
     *     max = 10,
     *     minMessage = "De gebruikersnaam moet minimaal 2 karakters lang zijn",
     *     maxMessage = "De gebruikersnaam mag maximaal 10 karakters lang zijn"
     * )
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
     * @Assert\NotBlank()
     * @Assert\Email(message="Dit is geen geldig e-mailadres")
     */
    private $email = '';

    /**
     * @var string
     * @ORM\Column(name="cookie_ok", type="string", length=3, nullable=false)
     */
    private $cookieOk = self::COOKIE_UNKNOWN;

    /**
     * @var string
     * @ORM\Column(name="actkey", type="string", length=32, nullable=false)
     */
    private $activationKey = '0';

    /**
     * @var DateTime
     * @ORM\Column(name="regdate", type="datetime", nullable=false)
     */
    private $registrationDate;

    /**
     * @var array
     * @ORM\Column(name="roles", type="array", nullable=false)
     */
    private $roles = [];

    /**
     * @var UserInfo
     * @ORM\OneToOne(targetEntity="App\Entity\UserInfo", mappedBy="user")
     */
    private $info;

    /**
     * @var UserLastVisit|null
     * @ORM\OneToOne(targetEntity="App\Entity\UserLastVisit", mappedBy="user")
     */
    private $lastVisit;

    /**
     * @var Group[]
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", mappedBy="users")
     */
    private $groups;

    /**
     * @var ForumFavorite[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumFavorite", mappedBy="user")
     */
    private $forumFavorites;

    /**
     * @var ForumForum[]
     * @ORM\ManyToMany(targetEntity="App\Entity\ForumForum", mappedBy="moderators")
     */
    private $moderatedForums;

    /**
     * @var Spot[]
     * @ORM\OneToMany(targetEntity="App\Entity\Spot", mappedBy="user")
     */
    private $spots;

    /**
     * @var UserPreferenceValue[]
     * @ORM\OneToMany(targetEntity="App\Entity\UserPreferenceValue", mappedBy="user")
     */
    private $preferences;

    /**
     *
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->forumFavorites = new ArrayCollection();
        $this->moderatedForums = new ArrayCollection();
        $this->spots = new ArrayCollection();
        $this->preferences = new ArrayCollection();
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
     * @return User
     */
    public function setId(int $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return User
     */
    public function setActive(bool $active): User
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
     * @return Group[]
     */
    public function getGroups(): array
    {
        return $this->groups->toArray();
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

    /**
     *
     */
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }
        return $roles;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * @param string $role
     * @return User
     */
    public function addRole(string $role): User
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    /**
     * @return UserInfo
     */
    public function getInfo(): UserInfo
    {
        return $this->info;
    }

    /**
     * @param UserInfo $info
     * @return User
     */
    public function setInfo(UserInfo $info): User
    {
        $this->info = $info;
        return $this;
    }

    /**
     * @return UserLastVisit|null
     */
    public function getLastVisit(): ?UserLastVisit
    {
        return $this->lastVisit;
    }

    /**
     * @param UserLastVisit $lastVisit
     * @return User
     */
    public function setLastVisit(UserLastVisit $lastVisit): User
    {
        $this->lastVisit = $lastVisit;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @param ForumFavorite $forumFavorite
     * @return User
     */
    public function addForumFavorite(ForumFavorite $forumFavorite): User
    {
        $this->forumFavorites[] = $forumFavorite;
        return $this;
    }

    /**
     * @return ForumFavorite[]
     */
    public function getForumFavorites(): array
    {
        return $this->forumFavorites->toArray();
    }

    /**
     * @param ForumDiscussion $discussion
     * @return bool
     */
    public function isForumFavorite(ForumDiscussion $discussion): bool
    {
        foreach ($this->getForumFavorites() as $forumFavorite) {
            if ($forumFavorite->getDiscussion() === $discussion) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param ForumForum $forumForum
     * @return User
     */
    public function addModeratedForum(ForumForum $forumForum): User
    {
        $this->moderatedForums[] = $forumForum;
        return $this;
    }

    /**
     * @return ForumForum[]
     */
    public function getModeratedForums(): array
    {
        return $this->moderatedForums->toArray();
    }

    /**
     * @param Spot $spot
     * @return User
     */
    public function addSpot(Spot $spot): User
    {
        $this->spots[] = $spot;
        return $this;
    }

    /**
     * @return Spot[]
     */
    public function getSpots(): array
    {
        return $this->spots->toArray();
    }

    /**
     * @param UserPreferenceValue $userPreferenceValue
     * @return User
     */
    public function addPreference(UserPreferenceValue $userPreferenceValue): User
    {
        $this->preferences[] = $userPreferenceValue;
        return $this;
    }

    /**
     * @return UserPreferenceValue[]
     */
    public function getPreferences(): array
    {
        return $this->preferences->toArray();
    }
}
