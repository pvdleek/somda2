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
    private $user;

    /**
     * @var UserPreference
     * @ORM\ManyToOne(targetEntity="App\Entity\UserPreference")
     * @ORM\JoinColumn(name="prefid", referencedColumnName="prefid")
     * @ORM\Id
     */
    private $preference;

    /**
     * @var string
     * @ORM\Column(name="value", type="string", length=200, nullable=false)
     */
    private $value = '';

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return UserPreferenceValue
     */
    public function setUser(User $user): UserPreferenceValue
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return UserPreference
     */
    public function getPreference(): UserPreference
    {
        return $this->preference;
    }

    /**
     * @param UserPreference $preference
     * @return UserPreferenceValue
     */
    public function setPreference(UserPreference $preference): UserPreferenceValue
    {
        $this->preference = $preference;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return UserPreferenceValue
     */
    public function setValue(string $value): UserPreferenceValue
    {
        $this->value = $value;
        return $this;
    }
}
