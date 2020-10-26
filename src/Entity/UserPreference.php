<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="usp_user_preference", indexes={@ORM\Index(name="IDX_usp_key", columns={"usp_key"})})
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

    public const KEY_FORCE_DESKTOP = 'force_desktop';

    public const KEY_MY_SPOTS_DEFAULT_NUMBER = 'myspots_default_nr';
    public const KEY_MY_SPOTS_DEFAULT_OFFSET = 'myspots_default_start';
    public const KEY_MY_SPOTS_SORT_ORDER_1 = 'myspots_sort';
    public const KEY_MY_SPOTS_SORT_METHOD_1 = 'myspots_sort_method';
    public const KEY_MY_SPOTS_SORT_ORDER_2 = 'myspots_sort2';
    public const KEY_MY_SPOTS_SORT_METHOD_2 = 'myspots_sort_method2';

    public const KEY_DEFAULT_SPOT_LOCATION = 'default_spot_place';

    public const KEY_FORUM_SIGNATURE = 'forum_signature';
    public const KEY_FORUM_NEW_TO_OLD = 'forum_new_to_old';
    public const KEY_FORUM_MAIL_FOR_ALL_FAVORITES = 'mail_all_favorites';

    public const KEY_MAIL_LAST_MINUTE_SPECIAL_ROUTE = 'mail_drgls';

    public const KEY_VALUES = [
        self::KEY_HOME_LAYOUT,
        self::KEY_HOME_MAX_NEWS,
        self::KEY_HOME_MAX_SPECIAL_ROUTES,
        self::KEY_HOME_MAX_SPOTS,
        self::KEY_HOME_MAX_FORUM_POSTS,
        self::KEY_HOME_MAX_FORUM_POSTS_WRONG_SPOTS,
        self::KEY_HOME_DISPLAY_POLL_AFTER_VOTING,
        self::KEY_MY_SPOTS_DEFAULT_NUMBER,
        self::KEY_MY_SPOTS_DEFAULT_OFFSET,
        self::KEY_MY_SPOTS_SORT_ORDER_1,
        self::KEY_MY_SPOTS_SORT_METHOD_1,
        self::KEY_MY_SPOTS_SORT_ORDER_2,
        self::KEY_MY_SPOTS_SORT_METHOD_2,
        self::KEY_DEFAULT_SPOT_LOCATION,
        self::KEY_FORUM_SIGNATURE,
        self::KEY_FORUM_NEW_TO_OLD,
        self::KEY_FORUM_MAIL_FOR_ALL_FAVORITES,
        self::KEY_MAIL_LAST_MINUTE_SPECIAL_ROUTE,
    ];

    /**
     * @var int|null
     * @ORM\Column(name="usp_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="usp_key", type="string", length=25, nullable=false)
     * @Assert\Choice(choices=UserPreference::KEY_VALUES)
     */
    public string $key;

    /**
     * @var string
     * @ORM\Column(name="usp_type", type="string", length=50, nullable=false)
     */
    public string $type = '';

    /**
     * @var string
     * @ORM\Column(name="usp_description", type="string", length=90, nullable=false)
     */
    public string $description = '';

    /**
     * @var string
     * @ORM\Column(name="usp_default_value", type="string", length=200, nullable=false)
     */
    public string $defaultValue = '';

    /**
     * @var int
     * @ORM\Column(name="usp_order", type="integer", nullable=false)
     */
    public int $order = 0;
}
