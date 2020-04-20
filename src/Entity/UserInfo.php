<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="somda_users_info", indexes={@ORM\Index(name="idx_49074_gebdatum", columns={"gebdatum"})})
 * @ORM\Entity
 */
class UserInfo
{
    public const GENDER_UNKNOWN = 0;
    public const GENDER_MALE = 1;
    public const GENDER_FEMALE = 2;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="info")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     */
    public $user;

    /**
     * @var string
     * @ORM\Column(name="avatar", type="string", length=30, nullable=false, options={"default"="_blank.gif"})
     */
    public $avatar = '_blank.gif';

    /**
     * @var string|null
     * @ORM\Column(name="website", type="string", length=75, nullable=true)
     */
    public $website;

    /**
     * @var string|null
     * @ORM\Column(name="city", type="string", length=50, nullable=true)
     * @Assert\Length(
     *     min = 2,
     *     max = 50,
     *     minMessage = "De woonplaats moet minimaal 2 karakters lang zijn",
     *     maxMessage = "De woonplaats mag maximaal 50 karakters lang zijn"
     * )
     */
    public $city;

    /**
     * @var string|null
     * @ORM\Column(name="skype", type="string", length=60, nullable=true)
     */
    public $skype;

    /**
     * @var int
     * @ORM\Column(name="geslacht", type="smallint", nullable=false, options={"default"="0"})
     */
    public $gender = 0;

    /**
     * @var DateTime|null
     * @ORM\Column(name="gebdatum", type="date", nullable=true)
     */
    public $birthDate;

    /**
     * @var int|null
     * @ORM\Column(name="mob_tel", type="bigint", nullable=true)
     * @Assert\Length(
     *     min = 11,
     *     max = 11,
     *     minMessage = "Jouw mobiele nummer moet exact 11 karakters zijn (startend met 316)",
     *     maxMessage = "Jouw mobiele nummer moet exact 11 karakters zijn (startend met 316)",
     * )
     */
    public $mobilePhone;

    /**
     * @var string|null
     * @ORM\Column(name="twitter_account", type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255,
     *     maxMessage = "Jouw Twitter account mag maximaal 255 karakters lang zijn",
     * )
     */
    public $twitterAccount;

    /**
     * @var string|null
     * @ORM\Column(name="facebook_account", type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255,
     *     maxMessage = "Jouw Facebook account mag maximaal 255 karakters lang zijn",
     * )
     */
    public $facebookAccount;

    /**
     * @var string|null
     * @ORM\Column(name="flickr_account", type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255,
     *     maxMessage = "Jouw Flickr account mag maximaal 255 karakters lang zijn",
     * )
     */
    public $flickrAccount;

    /**
     * @var string|null
     * @ORM\Column(name="youtube_account", type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255,
     *     maxMessage = "Jouw Youtube account mag maximaal 255 karakters lang zijn",
     * )
     */
    public $youtubeAccount;

    /**
     * @var string|null
     * @ORM\Column(name="info", type="text", length=0, nullable=true)
     */
    public $info;

    /**
     * @var UserCompany|null
     * @ORM\ManyToOne(targetEntity="App\Entity\UserCompany")
     * @ORM\JoinColumn(name="bedrijf_id", referencedColumnName="bedrijf_id")
     */
    public $company;
}
