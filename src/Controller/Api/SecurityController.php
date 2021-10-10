<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Generics\RoleGenerics;
use App\Helpers\UserHelper;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Monolog\Logger;
use Nelmio\ApiDocBundle\Annotation\Model;
use Psr\Log\LoggerInterface;
use OpenApi\Annotations as OA;
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
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     * @param LoggerInterface $logger
     */
    public function __construct(ManagerRegistry $doctrine, UserHelper $userHelper, LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @return Response
     * @OA\Post(
     *     description="Authenticates a user",
     *     @OA\Parameter(
     *         in="body",
     *         name="body",
     *         @OA\Schema(
     *             @OA\Property(property="username", type="string", description="The username to authenticate"),
     *             @OA\Property(property="password", type="string", description="The password to authenticate"),
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="The user is authenticated",
     *     @OA\Schema(
     *         @OA\Property(property="data", type="array", @OA\Items(ref=@Model(type=User::class))),
     *     ),
     * )
     * @OA\Response(response=400, description="The request is malformed")
     * @OA\Response(
     *     response=401,
     *     description="Authentication failed",
     *     @OA\Schema(
     *         @OA\Property(description="Description of the error", property="error", type="string"),
     *     ),
     * )
     * @OA\Tag(name="Security")
     */
    public function loginAction(Request $request): Response
    {
        // If we reach this point, the user was successfully logged in, so we look the user up and return it
        $userInformation = (array)json_decode($request->getContent(), true);
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['username' => $userInformation['username']]);

        return $this->handleView($this->view(['data' => $user], 200));
    }

    /**
     * @param int $id
     * @param string $token
     * @return Response
     * @OA\Parameter(description="ID of the user to verify", in="path", name="id", type="integer")
     * @OA\Parameter(description="Token of the user to verify", in="path", name="token", type="string")
     * @OA\Response(
     *     response=200,
     *     description="Verifies an existing user-token",
     *     @OA\Schema(
     *         @OA\Property(property="data", type="array", @OA\Items(ref=@Model(type=User::class))),
     *     ),
     * )
     * @OA\Response(
     *     response=401,
     *     description="Verification ot the token failed",
     *     @OA\Schema(@OA\Property(description="Description of the error", property="error", type="string")),
     * )
     * @OA\Tag(name="Security")
     */
    public function verifyAction(int $id, string $token): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        $user = $this->doctrine->getRepository(User::class)->findOneBy(
            ['id' => $id, 'active' => true, 'apiToken' => $token]
        );
        if (is_null($user) || $user->apiTokenExpiryTimestamp <= new DateTime()) {
            return $this->handleView($this->view(['error' => 'This token is not valid'], 401));
        }

        $user->apiTokenExpiryTimestamp = new DateTime(User::API_TOKEN_VALIDITY);
        try {
            $this->doctrine->getManager()->flush();
        } catch (Exception $exception) {
            $this->logger->addRecord(
                Logger::CRITICAL,
                'Failed to set api-token-expiry-timestamp for user ' . $user->id
            );
        }

        return $this->handleView($this->view(['data' => $user], 200));
    }
}
