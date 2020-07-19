<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Helpers\UserHelper;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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

    private $logger;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     */
    public function __construct(ManagerRegistry $doctrine, UserHelper $userHelper, LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
        $this->logger = $logger;
    }

    /**
     * @return Response
     */
    public function loginAction(Request $request): Response
    {
        $this->logger->addRecord(Logger::ERROR, "Request for login " . $request->getContent());

        return $this->handleView($this->view(['data' => $this->userHelper->getUser()], 200));
    }

    /**
     * @IsGranted("ROLE_API_USER")
     * @param int $id
     * @param string $token
     * @return Response
     */
    public function verifyAction(int $id, string $token): Response
    {
        $user = $this->doctrine->getRepository(User::class)->findOneBy(
            ['id' => $id, 'active' => true, 'apiToken' => $token]
        );
        if (!is_null($user)) {
            if ($user->apiTokenExpiryTimestamp > new DateTime()) {
                $user->apiTokenExpiryTimestamp = new DateTime(User::API_TOKEN_VALIDITY);
                $this->doctrine->getManager()->flush();
            } else {
                $user = null;
            }
        }

        return $this->handleView($this->view(['data' => $user], 200));
    }
}
