<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\User;
use App\Entity\UserPreference;
use App\Entity\UserPreferenceValue;
use App\Exception\UnknownUserPreferenceKey;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Extension\RuntimeExtensionInterface;

class UserHelper implements RuntimeExtensionInterface
{
    private const ADMINISTRATOR_UID = 1;
    private const MODERATOR_UID = 2;

    public const KEY_API_USER_ID = 'SomdaUserId';
    public const KEY_API_TOKEN = 'SomdaApiToken';

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var UserInterface|null
     */
    private ?UserInterface $user = null;

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
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        if (is_null($this->user)) {
            $this->user = $this->security->getUser();
        }

        return $this->user instanceof User ? $this->user : null;
    }

    /**
     * @param string $role
     * @throws AccessDeniedException
     */
    public function denyAccessUnlessGranted(string $role): void
    {
        if (!$this->security->isGranted($role)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @param int $userId
     * @param string $apiToken
     */
    public function setFromApiRequest(int $userId, string $apiToken): void
    {
        $user = $this->doctrine->getRepository(UserInterface::class)->findOneBy(
            ['id' => $userId, 'active' => true, 'apiToken' => $apiToken]
        );
        if (!is_null($user)) {
            $this->user = $user;
        }
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
        return $this->user instanceof User && $this->security->isGranted('IS_AUTHENTICATED_REMEMBERED');
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
        if (!is_null($user = $this->getUser())) {
            $userPreferenceValue->user = $user;
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
