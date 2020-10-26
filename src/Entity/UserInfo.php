<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="usi_user_info", indexes={
 *     @ORM\Index(name="IDX_usi_usc_id", columns={"usi_usc_id"}),
 *     @ORM\Index(name="IDX_usi_birth_date", columns={"usi_birth_date"}),
 * })
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
     * @ORM\JoinColumn(name="usi_use_id", referencedColumnName="use_id")
     * @ORM\Id
     * @JMS\Exclude()
     */
    public User $user;

    /**
     * @var string
     * @ORM\Column(name="usi_avatar", type="string", length=30, nullable=false, options={"default"="_blank.png"})
     * @JMS\Expose()
     * @SWG\Property(description="Avatar of the user", maxLength=30, type="string")
     */
    public string $avatar = '_blank.png';

    /**
     * @var string|null
     * @ORM\Column(name="usi_website", type="string", length=75, nullable=true)
     * @JMS\Expose()
     * @SWG\Property(description="Website of the user", maxLength=75, type="string")
     */
    public ?string $website;

    /**
     * @var string|null
     * @ORM\Column(name="usi_city", type="string", length=50, nullable=true)
     * @Assert\Length(
     *     min = 2,
     *     max = 50,
     *     minMessage = "De woonplaats moet minimaal 2 karakters lang zijn",
     *     maxMessage = "De woonplaats mag maximaal 50 karakters lang zijn"
     * )
     * @JMS\Expose()
     * @SWG\Property(description="City of the user", maxLength=50, type="string")
     */
    public ?string $city;

    /**
     * @var string|null
     * @ORM\Column(name="usi_skype", type="string", length=60, nullable=true)
     * @JMS\Expose()
     * @SWG\Property(description="Skype of the user", maxLength=60, type="string")
     */
    public ?string $skype;

    /**
     * @var int
     * @ORM\Column(name="usi_gender", type="smallint", nullable=false, options={"default"="0"})
     * @JMS\Expose()
     * @SWG\Property(
     *     description="The gender of the user: '0' for unknown, '1' for male, '2' for female",
     *     maxLength=1,
     *     enum={"0","1","2"},
     *     type="string",
     * )
     */
    public int $gender = 0;

    /**
     * @var DateTime|null
     * @ORM\Column(name="usi_birth_date", type="date", nullable=true)
     * @JMS\Expose()
     * @SWG\Property(description="ISO-8601 timestamp of the birth-date (Y-m-dTH:i:sP)", type="string")
     */
    public ?DateTime $birthDate;

    /**
     * @var string|null
     * @ORM\Column(name="usi_mobile_phone", type="bigint", nullable=true)
     * @Assert\Length(
     *     min = 11,
     *     max = 11,
     *     minMessage = "Jouw mobiele nummer moet exact 11 karakters zijn (startend met 316)",
     *     maxMessage = "Jouw mobiele nummer moet exact 11 karakters zijn (startend met 316)",
     * )
     * @JMS\Expose()
     * @SWG\Property(description="Mobile phone of the user", type="integer")
     */
    public ?string $mobilePhone;

    /**
     * @var string|null
     * @ORM\Column(name="usi_twitter_account", type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255,
     *     maxMessage = "Jouw Twitter account mag maximaal 255 karakters lang zijn",
     * )
     * @JMS\Expose()
     * @SWG\Property(description="Twitter account of the user", maxLength=255, type="string")
     */
    public ?string $twitterAccount;

    /**
     * @var string|null
     * @ORM\Column(name="usi_facebook_account", type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255,
     *     maxMessage = "Jouw Facebook account mag maximaal 255 karakters lang zijn",
     * )
     * @JMS\Expose()
     * @SWG\Property(description="Facebook account of the user", maxLength=255, type="string")
     */
    public ?string $facebookAccount;

    /**
     * @var string|null
     * @ORM\Column(name="usi_flickr_account", type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255,
     *     maxMessage = "Jouw Flickr account mag maximaal 255 karakters lang zijn",
     * )
     * @JMS\Expose()
     * @SWG\Property(description="Flickr account of the user", maxLength=255, type="string")
     */
    public ?string $flickrAccount;

    /**
     * @var string|null
     * @ORM\Column(name="usi_youtube_account", type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 255,
     *     maxMessage = "Jouw Youtube account mag maximaal 255 karakters lang zijn",
     * )
     * @JMS\Expose()
     * @SWG\Property(description="Youtube account of the user", maxLength=255, type="string")
     */
    public ?string $youtubeAccount;

    /**
     * @var UserCompany|null
     * @ORM\ManyToOne(targetEntity="App\Entity\UserCompany")
     * @ORM\JoinColumn(name="usi_usc_id", referencedColumnName="usc_id")
     * @JMS\Exclude()
     */
    public ?UserCompany $company;
}
