<?php

namespace App\Helpers;

use App\Entity\User;
use App\Entity\UserPreference;
use App\Entity\UserPreferenceValue;
use App\Exception\UnknownUserPreferenceKey;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Extension\RuntimeExtensionInterface;

class UserHelper implements RuntimeExtensionInterface
{
    private const ADMINISTRATOR_UID = 1;
    private const MODERATOR_UID = 2;

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var Security
     */
    private Security $security;

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
     * @return UserInterface
     */
    public function getUser(): ?UserInterface
    {
        $user = null;
        if ($this->userIsLoggedIn()) {
            /**
             * @var UserInterface $user
             */
            $user = $this->security->getUser();
        }

        return $user instanceof User ? $user : null;
    }

    /**
     * @return UserInterface
     */
    public function getAdministratorUser(): UserInterface
    {
        return $this->doctrine->getRepository(User::class)->find(self::ADMINISTRATOR_UID);
    }

    /**
     * @return UserInterface
     */
    public function getModeratorUser(): UserInterface
    {
        return $this->doctrine->getRepository(User::class)->find(self::MODERATOR_UID);
    }

    /**
     * @return bool
     */
    public function userIsLoggedIn(): bool
    {
        return $this->security->isGranted('IS_AUTHENTICATED_REMEMBERED');
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
            throw new UnknownUserPreferenceKey('Preference with key "' . $key . '" does not exist');
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

    /**
     * @param UserInterface $user
     * @return string
     * @throws Exception
     */
    public function getSignatureForUser(UserInterface $user): string
    {
        foreach ($user->getPreferences() as $preference) {
            if ($preference->preference->key === UserPreference::KEY_FORUM_SIGNATURE) {
                return $preference->value;
            }
        }
        return '';
    }
}
