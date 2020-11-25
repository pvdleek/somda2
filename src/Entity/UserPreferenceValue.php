<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(name="somda_users_prefs")
 * @ORM\Entity
 */
class UserPreferenceValue
{
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="preferences")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     * @JMS\Exclude()
     */
    public User $user;

    /**
     * @var UserPreference
     * @ORM\ManyToOne(targetEntity="App\Entity\UserPreference")
     * @ORM\JoinColumn(name="prefid", referencedColumnName="prefid")
     * @ORM\Id
     * @JMS\Expose()
     * @SWG\Property(description="The setting", ref=@Model(type=UserPreference::class))
     */
    public UserPreference $preference;

    /**
     * @var string
     * @ORM\Column(name="value", type="string", length=200, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="The value of the user-setting", maxLength=200, type="string")
     */
    public string $value = '';
}
