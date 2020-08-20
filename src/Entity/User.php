<?php
declare(strict_types=1);

namespace App\Entity;

use App\Interfaces\User as UserInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *     name="somda_users",
 *     indexes={
 *         @ORM\Index(name="idx_49053_uname", columns={"username"}),
 *         @ORM\Index(name="idx_49076_active", columns={"active"}),
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\User")
 */
class User implements UserInterface
{
    public const COOKIE_UNKNOWN = '0';
    public const COOKIE_NOT_OK = 'nok';
    public const COOKIE_OK = 'ok';
    public const COOKIE_VALUES = [self::COOKIE_UNKNOWN, self::COOKIE_NOT_OK, self::COOKIE_OK];

    public const API_TOKEN_VALIDITY = '+1 year';

    /**
     * @var int|null
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var bool
     * @ORM\Column(name="active", type="boolean", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Is the user active", type="boolean")
     */
    public bool $active = false;

    /**
     * @var int
     * @ORM\Column(name="spots_ok", type="integer", nullable=false)
     * @JMS\Exclude()
     */
    public int $spotsOk = 0;

    /**
     * @var string
     * @ORM\Column(name="username", type="string", length=20, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 2,
     *     max = 20,
     *     minMessage = "De gebruikersnaam moet minimaal 2 karakters lang zijn",
     *     maxMessage = "De gebruikersnaam mag maximaal 20 karakters lang zijn"
     * )
     * @JMS\Expose()
     * @SWG\Property(description="Username", maxLength=20, type="string")
     */
    public string $username = '';

    /**
     * @var string|null
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     * @JMS\Expose()
     * @SWG\Property(description="Real name of the user", maxLength=100, type="string")
     */
    public ?string $name;

    /**
     * @var string
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     * @JMS\Exclude()
     */
    public string $password = '';

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=100, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Email(message="Dit is geen geldig e-mailadres")
     * @Assert\Length(
     *     max = 100,
     *     maxMessage = "Het e-mailadres mag maximaal 100 karakters lang zijn"
     * )
     * @JMS\Expose()
     * @SWG\Property(description="Email address of the user", maxLength=100, type="string")
     */
    public string $email = '';

    /**
     * @var string
     * @ORM\Column(name="cookie_ok", type="string", length=3, nullable=false)
     * @Assert\Choice(choices=User::COOKIE_VALUES)
     * @JMS\Exclude()
     */
    public string $cookieOk = self::COOKIE_UNKNOWN;

    /**
     * @var string|null
     * @ORM\Column(name="actkey", type="string", length=13, nullable=true)
     * @JMS\Exclude()
     */
    public ?string $activationKey;

    /**
     * @var DateTime
     * @ORM\Column(name="regdate", type="datetime", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="ISO-8601 timestamp of the registration of the user (Y-m-dTH:i:sP)", type="string")
     */
    public DateTime $registerTimestamp;

    /**
     * @var DateTime|null
     * @ORM\Column(name="ban_expire_timestamp", type="datetime", nullable=true)
     * @JMS\Exclude()
     */
    public ?DateTime $banExpireTimestamp;

    /**
     * @var DateTime|null
     * @ORM\Column(name="last_visit", type="datetime", nullable=true)
     * @JMS\Expose()
     * @SWG\Property(description="ISO-8601 timestamp of the last visit of the user (Y-m-dTH:i:sP)", type="string")
     */
    public ?DateTime $lastVisit;

    /**
     * @var string|null
     * @ORM\Column(name="api_token", type="string", length=23, nullable=true)
     * @JMS\Expose()
     * @SWG\Property(description="Token of the user, if logged in", maxLength=23, type="string")
     */
    public ?string $apiToken = null;

    /**
     * @var DateTime|null
     * @ORM\Column(name="api_token_expiry_timestamp", type="datetime", nullable=true)
     * @JMS\Exclude()
     */
    public ?DateTime $apiTokenExpiryTimestamp = null;

    /**
     * @var array
     * @ORM\Column(name="roles", type="array", nullable=false)
     * @JMS\Exclude()
     */
    public array $roles = [];

    /**
     * @var UserInfo
     * @ORM\OneToOne(targetEntity="App\Entity\UserInfo", mappedBy="user")
     * @JMS\Expose()
     */
    public UserInfo $info;

    /**
     * @var Group[]
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", mappedBy="users")
     * @JMS\Exclude()
     */
    private $groups;

    /**
     * @var ForumFavorite[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumFavorite", mappedBy="user")
     * @JMS\Exclude()
     */
    private $forumFavorites;

    /**
     * @var ForumForum[]
     * @ORM\ManyToMany(targetEntity="App\Entity\ForumForum", mappedBy="moderators")
     * @JMS\Exclude()
     */
    private $moderatedForums;

    /**
     * @var Spot[]
     * @ORM\OneToMany(targetEntity="App\Entity\Spot", mappedBy="user")
     * @JMS\Exclude()
     */
    private $spots;

    /**
     * @var UserPreferenceValue[]
     * @ORM\OneToMany(targetEntity="App\Entity\UserPreferenceValue", mappedBy="user")
     * @JMS\Exclude()
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

    /**
     * @return string
     * @JMS\VirtualProperty(name="signature")
     */
    public function getSignature(): string
    {
        foreach ($this->getPreferences() as $preference) {
            if ($preference->preference->key === UserPreference::KEY_FORUM_SIGNATURE) {
                return $preference->value;
            }
        }
        return '';
    }
}
