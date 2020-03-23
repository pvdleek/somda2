<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use MsgPhp\User\User as BaseUser;
use MsgPhp\User\UserId;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="somda_users", indexes={@ORM\Index(name="idx_49053_uname", columns={"username"})})
 * @ORM\Entity
 */
class User extends BaseUser implements UserInterface
{
    /**
     * @var int
     * @ORM\Column(name="uid", type="msgphp_user_id", nullable=false)
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
     * @ORM\Column(name="spots_ok", type="integer", nullable=false)
     */
    private $spotsOk = 0;

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
     * @ORM\Column(name="roles", type="array", nullable=false)
     */
    private $roles = [];

    /**
     * @var UserInfo
     * @ORM\OneToOne(targetEntity="App\Entity\UserInfo", mappedBy="user")
     */
    private $info;

    /**
     * @var UserLastVisit
     * @ORM\OneToOne(targetEntity="App\Entity\UserLastVisit", mappedBy="user")
     */
    private $lastVisit;

    /**
     * @var Group
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", mappedBy="users")
     */
    private $groups;

    /**
     * @var ForumFavorite[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumFavorite", mappedBy="user")
     */
    private $forumFavorites;

    /**
     * @var ForumForum
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
     * @param UserId $id
     */
    public function __construct(UserId $id)
    {
        $this->id = $id;

        $this->forumFavorites = new ArrayCollection();
        $this->moderatedForums = new ArrayCollection();
        $this->spots = new ArrayCollection();
        $this->preferences = new ArrayCollection();
    }

    /**
     * @return UserId
     */
    public function getId(): UserId
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
     * @return UserLastVisit
     */
    public function getLastVisit(): UserLastVisit
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
