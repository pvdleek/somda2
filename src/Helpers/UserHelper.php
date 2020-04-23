<?php

namespace App\Helpers;

use App\Entity\User;
use App\Entity\UserPreference;
use App\Entity\UserPreferenceValue;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\RuntimeExtensionInterface;

class UserHelper implements RuntimeExtensionInterface
{
    private const ADMINISTRATOR_UID = 1;
    private const MODERATOR_UID = 2;

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var Security
     */
    private $security;

    /**
     * @param ManagerRegistry $doctrine
     * @param Security $security
     */
    public function __construct(ManagerRegistry $doctrine, Security $security)
    {
        $this->doctrine = $doctrine;
        $this->security = $security;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        $user = null;
        if ($this->userIsLoggedIn()) {
            /**
             * @var User $user
             */
            $user = $this->security->getUser();
        }
        return $user;
    }

    /**
     * @return User
     */
    public function getAdministratorUser(): User
    {
        /**
         * @var User $user
         */
        $user = $this->doctrine->getRepository(User::class)->find(self::ADMINISTRATOR_UID);
        return $user;
    }

    /**
     * @return User
     */
    public function getModeratorUser(): User
    {
        /**
         * @var User $user
         */
        $user = $this->doctrine->getRepository(User::class)->find(self::MODERATOR_UID);
        return $user;
    }

    /**
     * @return bool
     */
    public function userIsLoggedIn(): bool
    {
        return $this->security->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * @throws Exception
     */
    public function saveVisit(): void
    {
        if ($this->userIsLoggedIn()) {
            $this->getUser()->lastVisit = new DateTime();
            $this->doctrine->getManager()->flush();
        }
    }

    /**
     * @param string $key
     * @return UserPreferenceValue
     * @throws Exception
     */
    public function getPreferenceByKey(string $key): UserPreferenceValue
    {
        if (!is_null($this->getUser())) {
            foreach ($this->getUser()->getPreferences() as $preference) {
                if ($preference->preference->key === $key) {
                    return $preference;
                }
            }
        }

        // Get and save the default value for this key
        /**
         * @var UserPreference $userPreference
         */
        $userPreference = $this->doctrine->getRepository(UserPreference::class)->findOneBy(['key' => $key]);
        if (is_null($userPreference)) {
            throw new Exception('Preference with key "' . $key . '" does not exist');
        }
        $userPreferenceValue = new UserPreferenceValue();
        $userPreferenceValue->preference = $userPreference;
        $userPreferenceValue->value = $userPreference->defaultValue;
        if (!is_null($this->getUser())) {
            $userPreferenceValue->user = $this->getUser();
            $this->doctrine->getManager()->persist($userPreferenceValue);
            $this->doctrine->getManager()->flush();
        }

        return $userPreferenceValue;
    }
}
