<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="somda_users", indexes={@ORM\Index(name="idx_49053_uname", columns={"username"})})
 * @ORM\Entity(repositoryClass="App\Repository\User")
 */
class User extends Entity implements UserInterface
{
    public const COOKIE_UNKNOWN = '0';
    public const COOKIE_NOT_OK = 'nok';
    public const COOKIE_OK = 'ok';
    public const COOKIE_VALUES = [self::COOKIE_UNKNOWN, self::COOKIE_NOT_OK, self::COOKIE_OK];

    /**
     * @var int
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var bool
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    public bool $active = false;

    /**
     * @var int
     * @ORM\Column(name="spots_ok", type="integer", nullable=false)
     */
    public int $spotsOk = 0;

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
    public string $username = '';

    /**
     * @var string|null
     * @ORM\Column(name="name", type="string", length=40, nullable=true)
     */
    public ?string $name;

    /**
     * @var string
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    public string $password = '';

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=60, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Email(message="Dit is geen geldig e-mailadres")
     */
    public string $email = '';

    /**
     * @var string
     * @ORM\Column(name="cookie_ok", type="string", length=3, nullable=false)
     * @Assert\Choice(choices=User::COOKIE_VALUES)
     */
    public string $cookieOk = self::COOKIE_UNKNOWN;

    /**
     * @var string|null
     * @ORM\Column(name="actkey", type="string", length=13, nullable=true)
     */
    public ?string $activationKey;

    /**
     * @var DateTime
     * @ORM\Column(name="regdate", type="datetime", nullable=false)
     */
    public DateTime $registerTimestamp;

    /**
     * @var DateTime|null
     * @ORM\Column(name="ban_expire_timestamp", type="datetime", nullable=true)
     */
    public ?DateTime $banExpireTimestamp;

    /**
     * @var DateTime|null
     * @ORM\Column(name="last_visit", type="datetime", nullable=true)
     */
    public ?DateTime $lastVisit;

    /**
     * @var array
     * @ORM\Column(name="roles", type="array", nullable=false)
     */
    public array $roles = [];

    /**
     * @var UserInfo
     * @ORM\OneToOne(targetEntity="App\Entity\UserInfo", mappedBy="user")
     */
    public UserInfo $info;

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
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     *
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roleArray = $this->roles;
        foreach ($this->getGroups() as $group) {
            $roleArray = array_merge($roleArray, $group->roles);
        }
        return $roleArray;
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
     * @param Group $group
     * @return User
     */
    public function addGroup(Group $group): User
    {
        $this->groups[] = $group;
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
            if ($forumFavorite->discussion === $discussion) {
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
