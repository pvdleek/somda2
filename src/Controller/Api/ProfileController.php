<?php

declare(strict_types=1);

namespace App\Controller\Api;

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
        private readonly UserHelper $user_helper,
    ) {
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
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if (!$this->user_helper->userIsLoggedIn()) {
            throw new AccessDeniedException('The user is not logged in');
        }

        return $this->handleView($this->view(['data' => $this->user_helper->getUser()], 200));
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
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if (!$this->user_helper->userIsLoggedIn()) {
            throw new AccessDeniedException('The user is not logged in');
        }

        $user = $this->user_helper->getUser();

        $user_information = (array) \json_decode($request->getContent(), true);
        $user->info->avatar = $user_information['avatar'];
        if (isset($user_information['birthDate'])) {
            $user->info->birth_date = \DateTime::createFromFormat('Y-m-d', $user_information['birthDate']);
        }
        if (isset($user_information['city'])) {
            $user->info->city = $user_information['city'];
        }
        $user->info->gender = (int) $user_information['gender'];
        if (isset($user_information['mobilePhone'])) {
            $user->info->mobile_phone = $user_information['mobilePhone'];
        }
        if (isset($user_information['skype'])) {
            $user->info->skype = $user_information['skype'];
        }
        if (isset($user_information['website'])) {
            $user->info->website = $user_information['website'];
        }
        if (isset($user_information['facebookAccount'])) {
            $user->info->facebook_account = $user_information['facebookAccount'];
        }
        if (isset($user_information['flickrAccount'])) {
            $user->info->flickr_account = $user_information['flickrAccount'];
        }
        if (isset($user_information['twitterAccount'])) {
            $user->info->twitter_account = $user_information['twitterAccount'];
        }
        if (isset($user_information['youtubeAccount'])) {
            $user->info->youtube_account = $user_information['youtubeAccount'];
        }

        $this->doctrine->getManager()->flush();

        return $this->handleView($this->view(['data' => $user], 200));
    }

    /**
     * @throws \Exception
     * @OA\Post(
     *     @OA\Parameter(in="formData", name="forum_signature", required=false, @OA\Schema(type="string", maxLength=200)),
     *     @OA\Parameter(in="formData", name="forum_new_to_old", required=true, @OA\Schema(type="integer", default=0, enum={0,1})),
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
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if (!$this->user_helper->userIsLoggedIn()) {
            throw new AccessDeniedException('The user is not logged in');
        }

        $user = $this->user_helper->getUser();

        $preferences = (array) \json_decode($request->getContent(), true);
        if (isset($preferences['forum_signature'])) {
            $this->user_helper->getPreferenceByKey(UserPreference::KEY_FORUM_SIGNATURE)->value = $preferences['forum_signature'];
        }
        if (isset($preferences['forum_new_to_old'])) {
            $this->user_helper->getPreferenceByKey(UserPreference::KEY_FORUM_NEW_TO_OLD)->value = $preferences['forum_new_to_old'];
        }
        if (isset($preferences['default_spot_place'])) {
            $this->user_helper->getPreferenceByKey(UserPreference::KEY_DEFAULT_SPOT_LOCATION)->value = $preferences['default_spot_place'];
        }

        $this->doctrine->getManager()->flush();

        return $this->handleView($this->view(['data' => $user], 200));
    }
}
