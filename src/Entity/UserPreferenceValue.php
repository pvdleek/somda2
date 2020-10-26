<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="upf_user_preference_value")
 * @ORM\Entity
 */
class UserPreferenceValue
{
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="preferences")
     * @ORM\JoinColumn(name="upf_use_id", referencedColumnName="use_id")
     * @ORM\Id
     */
    public User $user;

    /**
     * @var UserPreference
     * @ORM\ManyToOne(targetEntity="App\Entity\UserPreference")
     * @ORM\JoinColumn(name="upf_usp_id", referencedColumnName="usp_id")
     * @ORM\Id
     */
    public UserPreference $preference;

    /**
     * @var string
     * @ORM\Column(name="upf_value", type="string", length=200, nullable=false)
     */
    public string $value = '';
}
