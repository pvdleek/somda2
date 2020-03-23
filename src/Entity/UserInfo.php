<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_users_info", indexes={@ORM\Index(name="idx_49074_gebdatum", columns={"gebdatum"})})
 * @ORM\Entity
 */
class UserInfo
{
    /**
     * @var User
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="info")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="avatar", type="string", length=30, nullable=false, options={"default"="_blank.gif"})
     */
    private $avatar = '_blank.gif';

    /**
     * @var string|null
     * @ORM\Column(name="website", type="string", length=75, nullable=true)
     */
    private $website;

    /**
     * @var string|null
     * @ORM\Column(name="city", type="string", length=50, nullable=true)
     */
    private $city;

    /**
     * @var string|null
     * @ORM\Column(name="skype", type="string", length=60, nullable=true)
     */
    private $skype;

    /**
     * @var int
     * @ORM\Column(name="geslacht", type="smallint", nullable=false, options={"default"="0"})
     */
    private $gender = 0;

    /**
     * @var DateTime|null
     * @ORM\Column(name="gebdatum", type="date", nullable=true)
     */
    private $birthDate;

    /**
     * @var int|null
     * @ORM\Column(name="mob_tel", type="bigint", nullable=true)
     */
    private $mobilePhone;

    /**
     * @var string|null
     * @ORM\Column(name="twitter_account", type="string", length=255, nullable=true)
     */
    private $twitterAccount;

    /**
     * @var string|null
     * @ORM\Column(name="facebook_account", type="string", length=255, nullable=true)
     */
    private $facebookAccount;

    /**
     * @var string|null
     * @ORM\Column(name="flickr_account", type="string", length=255, nullable=true)
     */
    private $flickrAccount;

    /**
     * @var string|null
     * @ORM\Column(name="youtube_account", type="string", length=255, nullable=true)
     */
    private $youtubeAccount;

    /**
     * @var string|null
     * @ORM\Column(name="info", type="text", length=0, nullable=true)
     */
    private $info;

    /**
     * @var UserCompany|null
     * @ORM\ManyToOne(targetEntity="App\Entity\UserCompany")
     * @ORM\JoinColumn(name="bedrijf_id", referencedColumnName="bedrijf_id")
     */
    private $company;

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return UserInfo
     */
    public function setUser(User $user): UserInfo
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getAvatar(): string
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     * @return UserInfo
     */
    public function setAvatar(string $avatar): UserInfo
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param string|null $website
     * @return UserInfo
     */
    public function setWebsite(?string $website): UserInfo
    {
        $this->website = $website;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     * @return UserInfo
     */
    public function setCity(?string $city): UserInfo
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSkype(): ?string
    {
        return $this->skype;
    }

    /**
     * @param string|null $skype
     * @return UserInfo
     */
    public function setSkype(?string $skype): UserInfo
    {
        $this->skype = $skype;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getGender(): ?int
    {
        return $this->gender;
    }

    /**
     * @param int|null $gender
     * @return UserInfo
     */
    public function setGender(?int $gender): UserInfo
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getBirthDate(): ?DateTime
    {
        return $this->birthDate;
    }

    /**
     * @param DateTime|null $birthDate
     * @return UserInfo
     */
    public function setBirthDate(?DateTime $birthDate): UserInfo
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMobilePhone(): ?int
    {
        return $this->mobilePhone;
    }

    /**
     * @param int|null $mobilePhone
     * @return UserInfo
     */
    public function setMobilePhone(?int $mobilePhone): UserInfo
    {
        $this->mobilePhone = $mobilePhone;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTwitterAccount(): ?string
    {
        return $this->twitterAccount;
    }

    /**
     * @param string|null $twitterAccount
     * @return UserInfo
     */
    public function setTwitterAccount(?string $twitterAccount): UserInfo
    {
        $this->twitterAccount = $twitterAccount;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFacebookAccount(): ?string
    {
        return $this->facebookAccount;
    }

    /**
     * @param string|null $facebookAccount
     * @return UserInfo
     */
    public function setFacebookAccount(?string $facebookAccount): UserInfo
    {
        $this->facebookAccount = $facebookAccount;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFlickrAccount(): ?string
    {
        return $this->flickrAccount;
    }

    /**
     * @param string|null $flickrAccount
     * @return UserInfo
     */
    public function setFlickrAccount(?string $flickrAccount): UserInfo
    {
        $this->flickrAccount = $flickrAccount;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getYoutubeAccount(): ?string
    {
        return $this->youtubeAccount;
    }

    /**
     * @param string|null $youtubeAccount
     * @return UserInfo
     */
    public function setYoutubeAccount(?string $youtubeAccount): UserInfo
    {
        $this->youtubeAccount = $youtubeAccount;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInfo(): ?string
    {
        return $this->info;
    }

    /**
     * @param string|null $info
     * @return UserInfo
     */
    public function setInfo(?string $info): UserInfo
    {
        $this->info = $info;
        return $this;
    }

    /**
     * @return UserCompany|null
     */
    public function getCompany(): ?UserCompany
    {
        return $this->company;
    }

    /**
     * @param UserCompany|null $company
     * @return UserInfo
     */
    public function setCompany(?UserCompany $company): UserInfo
    {
        $this->company = $company;
        return $this;
    }
}
