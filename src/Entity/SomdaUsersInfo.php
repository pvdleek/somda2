<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaUsersInfo
 *
 * @ORM\Table(name="somda_users_info", indexes={@ORM\Index(name="idx_49074_gebdatum", columns={"gebdatum"})})
 * @ORM\Entity
 */
class SomdaUsersInfo
{
    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $uid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="avatar", type="string", length=30, nullable=true, options={"default"="_blank.gif"})
     */
    private $avatar = '_blank.gif';

    /**
     * @var string|null
     *
     * @ORM\Column(name="icq", type="string", length=15, nullable=true)
     */
    private $icq;

    /**
     * @var string|null
     *
     * @ORM\Column(name="website", type="string", length=75, nullable=true)
     */
    private $website;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=50, nullable=true)
     */
    private $city;

    /**
     * @var string|null
     *
     * @ORM\Column(name="skype", type="string", length=60, nullable=true)
     */
    private $skype;

    /**
     * @var int|null
     *
     * @ORM\Column(name="geslacht", type="bigint", nullable=true)
     */
    private $geslacht = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="gebdatum", type="date", nullable=true)
     */
    private $gebdatum;

    /**
     * @var int|null
     *
     * @ORM\Column(name="mob_tel", type="bigint", nullable=true)
     */
    private $mobTel;

    /**
     * @var int|null
     *
     * @ORM\Column(name="bedrijf_id", type="bigint", nullable=true)
     */
    private $bedrijfId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="twitter_account", type="string", length=255, nullable=true)
     */
    private $twitterAccount;

    /**
     * @var string|null
     *
     * @ORM\Column(name="facebook_account", type="string", length=255, nullable=true)
     */
    private $facebookAccount;

    /**
     * @var string|null
     *
     * @ORM\Column(name="flickr_account", type="string", length=255, nullable=true)
     */
    private $flickrAccount;

    /**
     * @var string|null
     *
     * @ORM\Column(name="youtube_account", type="string", length=255, nullable=true)
     */
    private $youtubeAccount;

    /**
     * @var string|null
     *
     * @ORM\Column(name="info", type="text", length=0, nullable=true)
     */
    private $info;


}
