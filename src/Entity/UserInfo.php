<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'somda_users_info')]

class UserInfo
{
    public const GENDER_UNKNOWN = 0;
    public const GENDER_MALE = 1;
    public const GENDER_FEMALE = 2;

    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'info')]
    #[ORM\JoinColumn(name: 'uid', referencedColumnName: 'uid')]
    public ?User $user = null;

    #[ORM\Column(length: 30, nullable: false, options: ['default' => '_blank.png'])]
    public string $avatar = '_blank.png';

    #[ORM\Column(length: 75, nullable: true)]
    public ?string $website = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(min: 2, max: 50, minMessage: 'De woonplaats moet minimaal 2 karakters lang zijn', maxMessage: 'De woonplaats mag maximaal 50 karakters lang zijn')]
    public ?string $city = null;

    #[ORM\Column(length: 60, nullable: true)]
    public ?string $skype = null;

    #[ORM\Column(name: 'geslacht', type: 'smallint', nullable: false, options: ['default' => 0])]
    public int $gender = 0;

    #[ORM\Column(name: 'gebdatum', type: 'date', nullable: true)]
    public ?\DateTime $birth_date = null;

    #[ORM\Column(name: 'mob_tel', type: 'bigint', nullable: true)]
    #[Assert\Length(min: 11, max: 11, minMessage: 'Jouw mobiele nummer moet exact 11 karakters zijn (startend met 316)', maxMessage: 'Jouw mobiele nummer moet exact 11 karakters zijn (startend met 316)')]
    public ?int $mobile_phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Jouw Twitter account mag maximaal 255 karakters lang zijn')]
    public ?string $twitter_account = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Jouw Facebook account mag maximaal 255 karakters lang zijn')]
    public ?string $facebook_account = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Jouw Flickr account mag maximaal 255 karakters lang zijn')]
    public ?string $flickr_account = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Jouw Youtube account mag maximaal 255 karakters lang zijn')]
    public ?string $youtube_account = null;

    #[ORM\ManyToOne(targetEntity: UserCompany::class)]
    #[ORM\JoinColumn(name: 'bedrijf_id', referencedColumnName: 'bedrijf_id')]
    public ?UserCompany $company = null;
}
