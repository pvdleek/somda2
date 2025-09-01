<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_banner_customer_user')]
class BannerCustomerUser
{
    #[ORM\Column(nullable: false, options: ['default' => false])]
    public bool $allowed_new = false;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    public bool $allowed_max_views = false;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    public bool $allowed_max_hits = false;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    public bool $allowed_max_date = false;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    public bool $allowed_deactivate = false;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: BannerCustomer::class, inversedBy: 'customer_users')]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    public ?BannerCustomer $customer = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'uid', referencedColumnName: 'uid')]
    public ?User $user = null;
}
