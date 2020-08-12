<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Helpers\UserHelper;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends AbstractFOSRestController
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
     * @param Request $request
     * @return Response
     * @SWG\Post(
     *     description="Authenticates a user",
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         @SWG\Schema(
     *             @SWG\Property(property="username", type="string", description="The username to authenticate"),
     *             @SWG\Property(property="password", type="string", description="The password to authenticate"),
     *         )
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="The user is authenticated",
     *     @SWG\Property(property="data", type="array", @SWG\Items(ref=@Model(type=User::class))),
     * )
     * @SWG\Response(response=400, description="The request is malformed")
     * @SWG\Response(
     *     response=401,
     *     description="Authentication failed",
     *     @SWG\Schema(
     *         @SWG\Property(description="Description of the error", property="error", type="string"),
     *     ),
     * )
     * @SWG\Tag(name="Security")
     */
    public function loginAction(Request $request): Response
    {
        // If we reach this point, the user was successfully logged in, so we look the user up and return it
        $userInformation = json_decode($request->getContent(), true);
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['username' => $userInformation['username']]);

        return $this->handleView($this->view(['data' => $user], 200));
    }

    /**
     * @IsGranted("ROLE_API_USER")
     * @param int $id
     * @param string $token
     * @return Response
     * @SWG\Parameter(description="ID of the user to verify", in="path", name="id", type="integer")
     * @SWG\Parameter(description="Token of the user to verify", in="path", name="token", type="string")
     * @SWG\Response(
     *     response=200,
     *     description="Verifies an existing user-token",
     *     @SWG\Property(property="data", type="array", @SWG\Items(ref=@Model(type=User::class))),
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Verification ot the token failed",
     *     @SWG\Schema(@SWG\Property(description="Description of the error", property="error", type="string")),
     * )
     * @SWG\Tag(name="Security")
     */
    public function verifyAction(int $id, string $token): Response
    {
        $user = $this->doctrine->getRepository(User::class)->findOneBy(
            ['id' => $id, 'active' => true, 'apiToken' => $token]
        );
        if (is_null($user) || $user->apiTokenExpiryTimestamp <= new DateTime()) {
            return $this->handleView($this->view(['error' => 'This token is not valid'], 401));
        }

        $user->apiTokenExpiryTimestamp = new DateTime(User::API_TOKEN_VALIDITY);
        $this->doctrine->getManager()->flush();

        return $this->handleView($this->view(['data' => $user], 200));
    }
}
