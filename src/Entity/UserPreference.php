<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_prefs", indexes={@ORM\Index(name="idx_48215_sleutel", columns={"sleutel"})})
 * @ORM\Entity
 */
class UserPreference
{
    public const KEY_HOME_LAYOUT = 'layout';
    public const KEY_HOME_MAX_NEWS = 'max_news';
    public const KEY_HOME_MAX_SPECIAL_ROUTES = 'max_drgls';
    public const KEY_HOME_MAX_SPOTS = 'max_spots';
    public const KEY_HOME_MAX_FORUM_POSTS = 'max_posts';
    public const KEY_HOME_MAX_FORUM_POSTS_WRONG_SPOTS = 'max_foutespots';
    public const KEY_HOME_DISPLAY_POLL_AFTER_VOTING = 'display_poll';

    public const KEY_MY_SPOTS_DEFAULT_NUMBER = 'myspots_default_nr';
    public const KEY_MY_SPOTS_DEFAULT_OFFSET = 'myspots_default_start';
    public const KEY_MY_SPOTS_SORT_ORDER_1 = 'myspots_sort';
    public const KEY_MY_SPOTS_SORT_METHOD_1 = 'myspots_sort_method';
    public const KEY_MY_SPOTS_SORT_ORDER_2 = 'myspots_sort2';
    public const KEY_MY_SPOTS_SORT_METHOD_2 = 'myspots_sort_method2';

    public const KEY_DEFAULT_SPOT_LOCATION = 'default_spot_place';

    public const KEY_FORUM_SIGNATURE = 'forum_signature';
    public const KEY_FORUM_MAIL_FOR_ALL_FAVORITES = 'mail_all_favorites';

    public const KEY_MAIL_LAST_MINUTE_SPECIAL_ROUTE = 'mail_drgls';

    /**
     * @var int
     * @ORM\Column(name="prefid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="sleutel", type="string", length=25, nullable=false)
     */
    private $key;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=50, nullable=false)
     */
    private $type = '';

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=90, nullable=false)
     */
    private $description = '';

    /**
     * @var string
     * @ORM\Column(name="default_value", type="string", length=100, nullable=false)
     */
    private $defaultValue = '';

    /**
     * @var int
     * @ORM\Column(name="volgorde", type="bigint", nullable=false)
     */
    private $order;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return UserPreference
     */
    public function setId(int $id): UserPreference
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return UserPreference
     */
    public function setKey(string $key): UserPreference
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return UserPreference
     */
    public function setType(string $type): UserPreference
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return UserPreference
     */
    public function setDescription(string $description): UserPreference
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    /**
     * @param string $defaultValue
     * @return UserPreference
     */
    public function setDefaultValue(string $defaultValue): UserPreference
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return UserPreference
     */
    public function setOrder(int $order): UserPreference
    {
        $this->order = $order;
        return $this;
    }
}
