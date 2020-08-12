<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Helpers\UserHelper;
use DateTime;
use Exception;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
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
     * @IsGranted("ROLE_API_USER")
     * @return Response
     * @throws Exception
     * @SWG\Response(
     *     response=200,
     *     description="Returns the user profile",
     *     @SWG\Property(property="data", type="array", @SWG\Items(ref=@Model(type=User::class))),
     * )
     * @SWG\Tag(name="Profile")
     */
    public function indexAction(): Response
    {
        if (!$this->userHelper->userIsLoggedIn()) {
            throw new AccessDeniedException('The user is not logged in');
        }

        return $this->handleView($this->view(['data' => $this->userHelper->getUser()], 200));
    }

    /**
     * @IsGranted("ROLE_API_USER")
     * @param Request $request
     * @return Response
     * @throws Exception
     * @SWG\Post(
     *     @SWG\Parameter(in="formData", maxLength=30, name="avatar", required=true, type="string"),
     *     @SWG\Parameter(
     *         description="Y-m-d",
     *         in="formData",
     *         maxLength=10,
     *         minLength=10,
     *         name="birthDate",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(in="formData", maxLength=50, minLength=2, name="city", required=false, type="string"),
     *     @SWG\Parameter(default=0, enum={0,1,2}, in="formData", name="gender", required=true, type="integer"),
     *     @SWG\Parameter(
     *         description="Format is 316xxxxxxxx",
     *         in="formData",
     *         minLength=11,
     *         maxLength=11,
     *         name="mobilePhone",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(in="formData", maxLength=60, name="skype", required=false, type="string"),
     *     @SWG\Parameter(in="formData", maxLength=75, name="website", required=false, type="string"),
     *     @SWG\Parameter(in="formData", maxLength=255, name="facebookAccount", required=false, type="string"),
     *     @SWG\Parameter(in="formData", maxLength=255, name="flickrAccount", required=false, type="string"),
     *     @SWG\Parameter(in="formData", maxLength=255, name="twitterAccount", required=false, type="string"),
     *     @SWG\Parameter(in="formData", maxLength=255, name="youtubeAccount", required=false, type="string"),
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the updated user profile",
     *     @SWG\Property(property="data", type="array", @SWG\Items(ref=@Model(type=User::class))),
     * )
     * @SWG\Tag(name="Profile")
     */
    public function updateAction(Request $request): Response
    {
        if (!$this->userHelper->userIsLoggedIn()) {
            throw new AccessDeniedException('The user is not logged in');
        }

        $user = $this->userHelper->getUser();

        $userInformation = json_decode($request->getContent(), true);
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
}
