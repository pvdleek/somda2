<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'somda_users', indexes: [
    new ORM\Index(name: 'idx_somda_users__uname', columns: ['username']),
    new ORM\Index(name: 'idx_somda_users__active', columns: ['active']),
])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const API_TOKEN_VALIDITY = '+1 year';

    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'uid')]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Is the user active", type="boolean")
     */
    #[ORM\Column(nullable: false, options: ['default' => false])]
    public bool $active = false;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Username", maxLength=20, type="string")
     */
    #[ORM\Column(length: 20, nullable: false, options: ['default' => ''])]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 20, minMessage: 'De gebruikersnaam moet minimaal 2 karakters lang zijn', maxMessage: 'De gebruikersnaam mag maximaal 20 karakters lang zijn')]
    public string $username = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="Real name of the user", maxLength=100, type="string")
     */
    #[ORM\Column(length: 100, nullable: true, options: ['default' => ''])]
    public ?string $name = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\Column(length: 255, nullable: false, options: ['default' => ''])]
    public string $password = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="Email address of the user", maxLength=100, type="string")
     */
    #[Assert\NotBlank]
    #[Assert\Email(message: 'Dit is geen geldig e-mailadres')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Het e-mailadres mag maximaal 100 karakters lang zijn'
    )]
    #[ORM\Column(length: 100, nullable: false, options: ['default' => ''])]
    public string $email = '';

    /**
     * @JMS\Exclude()
     */
    #[ORM\Column(name: 'actkey', length: 13, nullable: true)]
    public ?string $activation_key = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the registration of the user (Y-m-dTH:i:sP)", type="string")
     */
    #[ORM\Column(name: 'regdate', type: 'datetime', nullable: true)]
    public ?\DateTime $register_timestamp = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTime $ban_expire_timestamp = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the last visit of the user (Y-m-dTH:i:sP)", type="string")
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTime $last_visit = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Token of the user, if logged in", maxLength=23, type="string")
     */
    #[ORM\Column(type: 'string', length: 23, nullable: true)]
    public ?string $api_token = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTime $api_token_expiry_timestamp = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\Column(type: 'array', nullable: false, options: ['default' => 'a:0:{}'])]
    public array $roles = [];

    /**
     * @JMS\Expose()
     */
    #[ORM\OneToOne(targetEntity: UserInfo::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    public ?UserInfo $info = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'users')]
    private Collection $groups;

    /**
     * @JMS\Exclude()
     */
    #[ORM\OneToMany(targetEntity: ForumFavorite::class, mappedBy: 'user')]
    private Collection $forum_favorites;

    /**
     * @JMS\Exclude()
     */
    #[ORM\OneToMany(targetEntity: ForumPostFavorite::class, mappedBy: 'user')]
    private Collection $forum_post_favorites;

    /**
     * @JMS\Exclude()
     */
    #[ORM\ManyToMany(targetEntity: ForumForum::class, mappedBy: 'moderators')]
    private Collection $moderated_forums;

    /**
     * @JMS\Exclude()
     */
    #[ORM\OneToMany(targetEntity: Spot::class, mappedBy: 'user')]
    private Collection $spots;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The user-settings", ref=@Model(type=UserPreferenceValue::class))
     */
    #[ORM\OneToMany(targetEntity: UserPreferenceValue::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $preferences;

    /**
     * @JMS\Exclude()
     */
    #[ORM\ManyToMany(targetEntity: News::class, mappedBy: 'user_reads')]
    #[ORM\JoinTable(
        name: 'somda_news_read',
        joinColumns: [new ORM\JoinColumn(name: 'uid', referencedColumnName: 'uid')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'newsid', referencedColumnName: 'newsid')]
    )]
    private Collection $news_reads;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->forum_favorites = new ArrayCollection();
        $this->forum_post_favorites = new ArrayCollection();
        $this->moderated_forums = new ArrayCollection();
        $this->spots = new ArrayCollection();
        $this->preferences = new ArrayCollection();
        $this->news_reads = new ArrayCollection();
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

    public function addForumFavorite(ForumFavorite $forum_favorite): User
    {
        $this->forum_favorites[] = $forum_favorite;
        return $this;
    }

    /**
     * @return ForumFavorite[]
     */
    public function getForumFavorites(): array
    {
        return $this->forum_favorites->toArray();
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

    public function addForumPostFavorite(ForumPostFavorite $forum_post_favorite): User
    {
        $this->forum_post_favorites[] = $forum_post_favorite;
        return $this;
    }

    /**
     * @return ForumPostFavorite[]
     */
    public function getForumPostFavorites(): array
    {
        return $this->forum_post_favorites->toArray();
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

    public function addModeratedForum(ForumForum $forum_forum): User
    {
        $this->moderated_forums[] = $forum_forum;
        return $this;
    }

    /**
     * @return ForumForum[]
     */
    public function getModeratedForums(): array
    {
        return $this->moderated_forums->toArray();
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

    public function addPreference(UserPreferenceValue $user_preference_value): User
    {
        $this->preferences[] = $user_preference_value;
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
        foreach ($this->news_reads->toArray() as $news_read) {
            $this->news_reads->removeElement($news_read);
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
