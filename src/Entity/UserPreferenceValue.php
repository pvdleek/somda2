<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     */
    public User $user;

    /**
     * @var UserPreference
     * @ORM\ManyToOne(targetEntity="App\Entity\UserPreference")
     * @ORM\JoinColumn(name="prefid", referencedColumnName="prefid")
     * @ORM\Id
     */
    public UserPreference $preference;

    /**
     * @var string
     * @ORM\Column(name="value", type="string", length=200, nullable=false)
     */
    public string $value = '';
}
