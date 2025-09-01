<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

#[ORM\Entity]
#[ORM\Table(name: 'somda_users_prefs')]
class UserPreferenceValue
{
    /**
     * @JMS\Exclude()
     */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'preferences')]
    #[ORM\JoinColumn(name: 'uid', referencedColumnName: 'uid')]
    public ?User $user = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The setting", ref=@Model(type=UserPreference::class))
     */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: UserPreference::class)]
    #[ORM\JoinColumn(name: 'prefid', referencedColumnName: 'prefid')]
    public ?UserPreference $preference = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The value of the user-setting", maxLength=200, type="string")
     */
    #[ORM\Column(length: 200, nullable: false, options: ['default' => ''])]
    public string $value = '';
}
