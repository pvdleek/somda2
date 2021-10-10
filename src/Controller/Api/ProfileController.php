<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\UserPreference;
use App\Generics\RoleGenerics;
use App\Helpers\UserHelper;
use DateTime;
use Exception;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends AbstractFOSRestController
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     */
    public function __construct(ManagerRegistry $doctrine, UserHelper $userHelper)
    {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
    }

    /**
     * @return Response
     * @throws Exception
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
     * @param Request $request
     * @return Response
     * @throws Exception
     * @OA\Post(
     *     @OA\Parameter(in="formData", maxLength=30, name="avatar", required=true, type="string"),
     *     @OA\Parameter(
     *         description="Y-m-d",
     *         in="formData",
     *         maxLength=10,
     *         minLength=10,
     *         name="birthDate",
     *         required=false,
     *         type="string",
     *     ),
     *     @OA\Parameter(in="formData", maxLength=50, minLength=2, name="city", required=false, type="string"),
     *     @OA\Parameter(default=0, enum={0,1,2}, in="formData", name="gender", required=true, type="integer"),
     *     @OA\Parameter(
     *         description="Format is 316xxxxxxxx",
     *         in="formData",
     *         minLength=11,
     *         maxLength=11,
     *         name="mobilePhone",
     *         required=false,
     *         type="string",
     *     ),
     *     @OA\Parameter(in="formData", maxLength=60, name="skype", required=false, type="string"),
     *     @OA\Parameter(in="formData", maxLength=75, name="website", required=false, type="string"),
     *     @OA\Parameter(in="formData", maxLength=255, name="facebookAccount", required=false, type="string"),
     *     @OA\Parameter(in="formData", maxLength=255, name="flickrAccount", required=false, type="string"),
     *     @OA\Parameter(in="formData", maxLength=255, name="twitterAccount", required=false, type="string"),
     *     @OA\Parameter(in="formData", maxLength=255, name="youtubeAccount", required=false, type="string"),
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

        $userInformation = (array)json_decode($request->getContent(), true);
        $user->info->avatar = $userInformation['avatar'];
        if (isset($userInformation['birthDate'])) {
            $user->info->birthDate = DateTime::createFromFormat('Y-m-d', $userInformation['birthDate']);
        }
        if (isset($userInformation['city'])) {
            $user->info->city = $userInformation['city'];
        }
        $user->info->gender = (int)$userInformation['gender'];
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
     * @param Request $request
     * @return Response
     * @throws Exception
     * @OA\Post(
     *     @OA\Parameter(in="formData", maxLength=200, name="forum_signature", required=false, type="string"),
     *     @OA\Parameter(default=0, enum={0,1}, in="formData", name="forum_new_to_old", required=true, type="integer"),
     *     @OA\Parameter(
     *         default=0,
     *         enum={0,1},
     *         in="formData",
     *         name="app_mark_forum_read",
     *         required=true,
     *         type="integer"
     *     ),
     *     @OA\Parameter(in="formData", maxLength=200, name="default_spot_place", required=false, type="string"),
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

        $preferences = (array)json_decode($request->getContent(), true);
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
