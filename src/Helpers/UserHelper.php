<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\Location;
use App\Entity\User;
use App\Entity\UserPreference;
use App\Entity\UserPreferenceValue;
use App\Exception\UnknownUserPreferenceKey;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Extension\RuntimeExtensionInterface;

class UserHelper implements RuntimeExtensionInterface
{
    private const ADMINISTRATOR_UID = 1;
    private const MODERATOR_UID = 2;

    public const KEY_API_USER_ID = 'SomdaUserId';
    public const KEY_API_TOKEN = 'SomdaApiToken';

    /**
     * @var UserInterface|null
     */
    private ?UserInterface $user = null;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly Security $security,
    ) {
    }

    public function getUser(): ?User
    {
        if (\is_null($this->user)) {
            $this->user = $this->security->getUser();
        }

        return $this->user instanceof User ? $this->user : null;
    }

    /**
     * @throws AccessDeniedException
     */
    public function denyAccessUnlessGranted(string $role): void
    {
        if (!$this->security->isGranted($role)) {
            throw new AccessDeniedException();
        }
    }

    public function setFromApiRequest(int $userId, string $apiToken): void
    {
        $user = $this->doctrine->getRepository(User::class)->findOneBy(
            ['id' => $userId, 'active' => true, 'apiToken' => $apiToken]
        );
        if (!\is_null($user)) {
            $this->user = $user;
            $this->security->login($user, 'form_login', 'main');
        }
    }

    public function getAdministratorUser(): UserInterface
    {
        return $this->doctrine->getRepository(User::class)->find(self::ADMINISTRATOR_UID);
    }

    public function getModeratorUser(): UserInterface
    {
        return $this->doctrine->getRepository(User::class)->find(self::MODERATOR_UID);
    }

    public function userIsLoggedIn(): bool
    {
        return $this->user instanceof User && $this->security->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * @throws \Exception
     */
    public function getPreferenceByKey(string $key): UserPreferenceValue
    {
        /**
         * @var UserPreference $userPreference
         */
        $userPreference = $this->doctrine->getRepository(UserPreference::class)->findOneBy(['key' => $key]);
        if (\is_null($userPreference)) {
            throw new UnknownUserPreferenceKey('Preference with key "' . $key . '" does not exist');
        }

        if (!\is_null($this->getUser())) {
            foreach ($this->getUser()->getPreferences() as $preference) {
                if ($preference->preference === $userPreference) {
                    return $preference;
                }
            }
        }

        // Get the default value for this key and save if user is logged in
        $userPreferenceValue = new UserPreferenceValue();
        $userPreferenceValue->preference = $userPreference;
        $userPreferenceValue->value = $userPreference->defaultValue;
        if (!\is_null($user = $this->getUser())) {
            $userPreferenceValue->user = $user;
            $this->doctrine->getManager()->persist($userPreferenceValue);
            $this->doctrine->getManager()->flush();
        }

        return $userPreferenceValue;
    }

    /**
     * @throws \Exception
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

    /**
     * @throws \Exception
     */
    public function getDefaultLocation(): ?Location
    {
        $location = null;
        $defaultLocation = $this->getPreferenceByKey(UserPreference::KEY_DEFAULT_SPOT_LOCATION);
        if (\strlen($defaultLocation->value) > 0) {
            /**
             * @var Location $location
             */
            $location = $this->doctrine->getRepository(Location::class)->findOneBy(['name' => $defaultLocation->value]);
        }
        return $location;
    }
}
