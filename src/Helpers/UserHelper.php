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
        if (null === $this->user) {
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

    public function getAdministratorUser(): User
    {
        return $this->doctrine->getRepository(User::class)->find(self::ADMINISTRATOR_UID);
    }

    public function getModeratorUser(): User
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
    public function getPreferenceByKey(string $key, bool $no_default = false): ?UserPreferenceValue
    {
        /**
         * @var UserPreference $user_preference
         */
        $user_preference = $this->doctrine->getRepository(UserPreference::class)->findOneBy(['key' => $key]);
        if (null === $user_preference) {
            throw new UnknownUserPreferenceKey('Preference with key "'.$key.'" does not exist');
        }

        if (null !== $this->getUser()) {
            foreach ($this->getUser()->getPreferences() as $preference) {
                if ($preference->preference === $user_preference) {
                    return $preference;
                }
            }
        }

        if ($no_default) {
            return null;
        }

        // Get the default value for this key and save if user is logged in
        $user_preference_value = new UserPreferenceValue();
        $user_preference_value->preference = $user_preference;
        $user_preference_value->value = $user_preference->default_value;
        if (null !== $user = $this->getUser()) {
            $user_preference_value->user = $user;
            $this->doctrine->getManager()->persist($user_preference_value);
            $this->doctrine->getManager()->flush();
        }

        return $user_preference_value;
    }

    /**
     * @throws \Exception
     */
    public function getSignatureForUser(User $user): string
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
        $default_location = $this->getPreferenceByKey(UserPreference::KEY_DEFAULT_SPOT_LOCATION);
        if (\strlen($default_location->value) > 0) {
            /**
             * @var Location $location
             */
            $location = $this->doctrine->getRepository(Location::class)->findOneBy(['name' => $default_location->value]);
        }
        
        return $location;
    }
}
