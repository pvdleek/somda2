<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="somda_prefs", indexes={@ORM\Index(name="idx_somda_prefs__sleutel", columns={"sleutel"})})
 * @ORM\Entity
 */
class UserPreference
{
    public const KEY_HOME_LAYOUT = 'layout';
    public const KEY_HOME_DESIGN = 'design';
    public const KEY_HOME_MAX_NEWS = 'max_news';
    public const KEY_HOME_MAX_SPECIAL_ROUTES = 'max_drgls';
    public const KEY_HOME_MAX_SPOTS = 'max_spots';
    public const KEY_HOME_MAX_FORUM_POSTS = 'max_posts';

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
        self::KEY_HOME_DESIGN,
        self::KEY_HOME_MAX_NEWS,
        self::KEY_HOME_MAX_SPECIAL_ROUTES,
        self::KEY_HOME_MAX_SPOTS,
        self::KEY_HOME_MAX_FORUM_POSTS,
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
     * @ORM\Column(name="prefid", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Exclude()
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="sleutel", type="string", length=25, nullable=false)
     * @Assert\Choice(choices=UserPreference::KEY_VALUES)
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", maxLength=25, type="string")
     */
    public string $key = '';

    /**
     * @ORM\Column(name="type", type="string", length=50, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Type of the value", maxLength=50, type="string")
     */
    public string $type = '';

    /**
     * @ORM\Column(name="description", type="string", length=90, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Description of the setting", maxLength=90, type="string")
     */
    public string $description = '';

    /**
     * @ORM\Column(name="default_value", type="string", length=200, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Default value", maxLength=200, type="string")
     */
    public string $defaultValue = '';

    /**
     * @ORM\Column(name="volgorde", type="smallint", nullable=false, options={"unsigned"=true})
     * @JMS\Exclude()
     */
    public int $order = 0;
}
