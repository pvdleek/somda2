<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *     name="somda_users",
 *     indexes={
 *         @ORM\Index(name="idx_somda_users__uname", columns={"username"}),
 *         @ORM\Index(name="idx_somda_users__active", columns={"active"}),
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\User")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const API_TOKEN_VALIDITY = '+1 year';

    /**
     * @ORM\Column(name="uid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="active", type="boolean", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Is the user active", type="boolean")
     */
    public bool $active = false;

    /**
     * @ORM\Column(name="spots_ok", type="smallint", nullable=false, options={"unsigned"=true})
     * @JMS\Exclude()
     */
    public int $spotsOk = 0;

    /**
     * @ORM\Column(name="username", type="string", length=20, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 2,
     *     max = 20,
     *     minMessage = "De gebruikersnaam moet minimaal 2 karakters lang zijn",
     *     maxMessage = "De gebruikersnaam mag maximaal 20 karakters lang zijn"
     * )
     * @JMS\Expose()
     * @OA\Property(description="Username", maxLength=20, type="string")
     */
    public string $username = '';

    /**
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     * @JMS\Expose()
     * @OA\Property(description="Real name of the user", maxLength=100, type="string")
     */
    public ?string $name = null;

    /**
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     * @JMS\Exclude()
     */
    public string $password = '';

    /**
     * @ORM\Column(name="email", type="string", length=100, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Email(message="Dit is geen geldig e-mailadres")
     * @Assert\Length(
     *     max = 100,
     *     maxMessage = "Het e-mailadres mag maximaal 100 karakters lang zijn"
     * )
     * @JMS\Expose()
     * @OA\Property(description="Email address of the user", maxLength=100, type="string")
     */
    public string $email = '';

    /**
     * @ORM\Column(name="actkey", type="string", length=13, nullable=true)
     * @JMS\Exclude()
     */
    public ?string $activationKey = null;

    /**
     * @ORM\Column(name="regdate", type="datetime", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the registration of the user (Y-m-dTH:i:sP)", type="string")
     */
    public ?\DateTime $registerTimestamp = null;

    /**
     * @ORM\Column(name="ban_expire_timestamp", type="datetime", nullable=true)
     * @JMS\Exclude()
     */
    public ?\DateTime $banExpireTimestamp = null;

    /**
     * @ORM\Column(name="last_visit", type="datetime", nullable=true)
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the last visit of the user (Y-m-dTH:i:sP)", type="string")
     */
    public ?\DateTime $lastVisit = null;

    /**
     * @ORM\Column(name="api_token", type="string", length=23, nullable=true)
     * @JMS\Expose()
     * @OA\Property(description="Token of the user, if logged in", maxLength=23, type="string")
     */
    public ?string $apiToken = null;

    /**
     * @ORM\Column(name="api_token_expiry_timestamp", type="datetime", nullable=true)
     * @JMS\Exclude()
     */
    public ?\DateTime $apiTokenExpiryTimestamp = null;

    /**
     * @ORM\Column(name="roles", type="array", nullable=false)
     * @JMS\Exclude()
     */
    public array $roles = [];

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserInfo", mappedBy="user")
     * @JMS\Expose()
     */
    public ?UserInfo $info = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", mappedBy="users")
     * @JMS\Exclude()
     */
    private $groups;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ForumFavorite", mappedBy="user")
     * @JMS\Exclude()
     */
    private $forumFavorites;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ForumPostFavorite", mappedBy="user")
     * @JMS\Exclude()
     */
    private $forumPostFavorites;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ForumForum", mappedBy="moderators")
     * @JMS\Exclude()
     */
    private $moderatedForums;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Spot", mappedBy="user")
     * @JMS\Exclude()
     */
    private $spots;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserPreferenceValue", mappedBy="user")
     * @JMS\Expose()
     * @OA\Property(description="The user-settings", ref=@Model(type=UserPreferenceValue::class))
     */
    private $preferences;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\News", mappedBy="userReads")
     * @ORM\JoinTable(name="somda_news_read",
     *      joinColumns={@ORM\JoinColumn(name="uid", referencedColumnName="uid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="newsid", referencedColumnName="newsid")}
     * )
     * @JMS\Exclude()
     */
    private $newsReads;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->forumFavorites = new ArrayCollection();
        $this->forumPostFavorites = new ArrayCollection();
        $this->moderatedForums = new ArrayCollection();
        $this->spots = new ArrayCollection();
        $this->preferences = new ArrayCollection();
        $this->newsReads = new ArrayCollection();
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function eraseCredentials(): void
    {
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getRoles(): array
    {
        $roleArray = $this->roles;
        foreach ($this->getGroups() as $group) {
            $roleArray = \array_merge($roleArray, $group->roles);
        }
        return $roleArray;
    }

    public function hasRole(string $role): bool
    {
        return \in_array($role, $this->getRoles());
    }

    public function addRole(string $role): User
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }
        return $this;
    }

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

    public function isForumFavorite(ForumDiscussion $discussion): bool
    {
        foreach ($this->getForumFavorites() as $forumFavorite) {
            if ($forumFavorite->discussion === $discussion) {
                return true;
            }
        }
        return false;
    }

    public function addForumPostFavorite(ForumPostFavorite $forumPostFavorite): User
    {
        $this->forumPostFavorites[] = $forumPostFavorite;
        return $this;
    }

    /**
     * @return ForumPostFavorite[]
     */
    public function getForumPostFavorites(): array
    {
        return $this->forumPostFavorites->toArray();
    }

    public function isPostFavorite(ForumPost $post): bool
    {
        foreach ($this->getForumPostFavorites() as $postFavorite) {
            if ($postFavorite->post === $post) {
                return true;
            }
        }
        return false;
    }

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

    public function removeAllNewsRead(): void
    {
        foreach ($this->newsReads->toArray() as $newsRead) {
            $this->newsReads->removeElement($newsRead);
        }
    }

    /**
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
