<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\UserPreference;
use App\Generics\RoleGenerics;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends AbstractFOSRestController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
    ) {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
    }

    /**
     * @throws \Exception
     * @OA\Response(
     *     response=200,
     *     description="Returns the user profile",
     *     @OA\Schema(
     *         @OA\Property(property="data", type="array", @OA\Items(ref=@Model(type=User::class))),
     *     ),
     * )
     * @OA\Tag(name="Profile")
     */
    public function indexAction(): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if (!$this->userHelper->userIsLoggedIn()) {
            throw new AccessDeniedException('The user is not logged in');
        }

        return $this->handleView($this->view(['data' => $this->userHelper->getUser()], 200));
    }

    /**
     * @throws \Exception
     * @OA\Post(
     *     @OA\Parameter(in="formData", name="avatar", required=true, @OA\Schema(type="string", maxLength=30)),
     *     @OA\Parameter(
     *         description="Y-m-d",
     *         in="formData",
     *         name="birthDate",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=10, minLength=10),
     *     ),
     *     @OA\Parameter(in="formData", name="city", required=false, @OA\Schema(type="string", maxLength=50, minLength=2)),
     *     @OA\Parameter(in="formData", name="gender", required=true, @OA\Schema(type="integer", default=0, enum={0,1,2})),
     *     @OA\Parameter(
     *         description="Format is 316xxxxxxxx",
     *         in="formData",
     *         name="mobilePhone",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=11, minLength=11),
     *     ),
     *     @OA\Parameter(in="formData", name="skype", required=false, @OA\Schema(type="string", maxLength=60)),
     *     @OA\Parameter(in="formData", name="website", required=false, @OA\Schema(type="string", maxLength=75)),
     *     @OA\Parameter(in="formData", name="facebookAccount", required=false, @OA\Schema(type="string", maxLength=255)),
     *     @OA\Parameter(in="formData", name="flickrAccount", required=false, @OA\Schema(type="string", maxLength=255)),
     *     @OA\Parameter(in="formData", name="twitterAccount", required=false, @OA\Schema(type="string", maxLength=255)),
     *     @OA\Parameter(in="formData", name="youtubeAccount", required=false, @OA\Schema(type="string", maxLength=255)),
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns the updated user profile",
     *     @OA\Schema(
     *         @OA\Property(property="data", type="array", @OA\Items(ref=@Model(type=User::class))),
     *     ),
     * )
     * @OA\Tag(name="Profile")
     */
    public function updateAction(Request $request): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if (!$this->userHelper->userIsLoggedIn()) {
            throw new AccessDeniedException('The user is not logged in');
        }

        $user = $this->userHelper->getUser();

        $userInformation = (array) \json_decode($request->getContent(), true);
        $user->info->avatar = $userInformation['avatar'];
        if (isset($userInformation['birthDate'])) {
            $user->info->birthDate = \DateTime::createFromFormat('Y-m-d', $userInformation['birthDate']);
        }
        if (isset($userInformation['city'])) {
            $user->info->city = $userInformation['city'];
        }
        $user->info->gender = (int) $userInformation['gender'];
        if (isset($userInformation['mobilePhone'])) {
            $user->info->mobilePhone = $userInformation['mobilePhone'];
        }
        if (isset($userInformation['skype'])) {
            $user->info->skype = $userInformation['skype'];
        }
        if (isset($userInformation['website'])) {
            $user->info->website = $userInformation['website'];
        }
        if (isset($userInformation['facebookAccount'])) {
            $user->info->facebookAccount = $userInformation['facebookAccount'];
        }
        if (isset($userInformation['flickrAccount'])) {
            $user->info->flickrAccount = $userInformation['flickrAccount'];
        }
        if (isset($userInformation['twitterAccount'])) {
            $user->info->twitterAccount = $userInformation['twitterAccount'];
        }
        if (isset($userInformation['youtubeAccount'])) {
            $user->info->youtubeAccount = $userInformation['youtubeAccount'];
        }

        $this->doctrine->getManager()->flush();

        return $this->handleView($this->view(['data' => $user], 200));
    }

    /**
     * @throws \Exception
     * @OA\Post(
     *     @OA\Parameter(in="formData", name="forum_signature", required=false, @OA\Schema(type="string", maxLength=200)),
     *     @OA\Parameter(in="formData", name="forum_new_to_old", required=true, @OA\Schema(type="integer", default=0, enum={0,1})),
     *     @OA\Parameter(
     *         in="formData",
     *         name="app_mark_forum_read",
     *         required=true,
     *         @OA\Schema(type="integer", enum={0,1}, default=0),
     *     ),
     *     @OA\Parameter(in="formData", name="default_spot_place", required=false, @OA\Schema(type="string", maxLength=200)),
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns the updated user profile",
     *     @OA\Schema(
     *         @OA\Property(property="data", type="array", @OA\Items(ref=@Model(type=User::class))),
     *     ),
     * )
     * @OA\Tag(name="Profile")
     */
    public function updatePreferencesAction(Request $request): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if (!$this->userHelper->userIsLoggedIn()) {
            throw new AccessDeniedException('The user is not logged in');
        }

        $user = $this->userHelper->getUser();

        $preferences = (array) \json_decode($request->getContent(), true);
        if (isset($preferences['forum_signature'])) {
            $this->userHelper->getPreferenceByKey(UserPreference::KEY_FORUM_SIGNATURE)->value =
                $preferences['forum_signature'];
        }
        if (isset($preferences['forum_new_to_old'])) {
            $this->userHelper->getPreferenceByKey(UserPreference::KEY_FORUM_NEW_TO_OLD)->value =
                (bool)$preferences['forum_new_to_old'];
        }
        if (isset($preferences['app_mark_forum_read'])) {
            $this->userHelper->getPreferenceByKey(UserPreference::KEY_APP_MARK_FORUM_READ)->value =
                (bool)$preferences['app_mark_forum_read'];
        }
        if (isset($preferences['default_spot_place'])) {
            $this->userHelper->getPreferenceByKey(UserPreference::KEY_DEFAULT_SPOT_LOCATION)->value =
                $preferences['default_spot_place'];
        }

        $this->doctrine->getManager()->flush();

        return $this->handleView($this->view(['data' => $user], 200));
    }
}
