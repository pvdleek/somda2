<?php
declare(strict_types=1);

namespace App\Listener;

use App\Entity\Log;
use App\Entity\User;
use App\Generics\DateGenerics;
use App\Helpers\UserHelper;
use DateTime;
use Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Stopwatch\Stopwatch;

class KernelListener implements EventSubscriberInterface
{
    use TargetPathTrait;

    private const STOPWATCH_NAME = 'main';

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var Stopwatch|null
     */
    private ?Stopwatch $stopwatch;

    private ?string $route;

    private ?array $routeParameters;

    /**
     * @param ManagerRegistry $doctrine
     * @param SessionInterface $session
     * @param UserHelper $userHelper
     */
    public function __construct(ManagerRegistry $doctrine, SessionInterface $session, UserHelper $userHelper)
    {
        $this->doctrine = $doctrine;
        $this->session = $session;
        $this->userHelper = $userHelper;

        $this->stopwatch = new Stopwatch(true);
        $this->stopwatch->start(self::STOPWATCH_NAME);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', -10],
            KernelEvents::REQUEST => ['onKernelRequest', -10],
            KernelEvents::TERMINATE => ['onKernelTerminate', -10],
            SecurityEvents::INTERACTIVE_LOGIN => ['onInteractiveLogin', -10],
        ];
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        if ($this->isApiRequest($event)) {
            $event->setResponse(new Response('{ "error": "' . $event->getThrowable()->getMessage() . '" }'));
        }
    }

    /**
     * @param RequestEvent $event
     * @throws Exception
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$this->isShouldExecuteEventHandler($event)) {
            return;
        }

        $this->route = (string)$event->getRequest()->attributes->get('_route');
        $this->routeParameters = (array)$event->getRequest()->attributes->get('_route_params');

        if ($this->isApiRequest($event)
            && $event->getRequest()->headers->has(UserHelper::KEY_API_USER_ID)
            && $event->getRequest()->headers->has(UserHelper::KEY_API_TOKEN)
        ) {
            $this->userHelper->setFromApiRequest(
                (int)$event->getRequest()->headers->get(UserHelper::KEY_API_USER_ID),
                $event->getRequest()->headers->get(UserHelper::KEY_API_TOKEN)
            );
        }

        if (!is_null($this->userHelper->getUser())
            && $this->userHelper->getUser()->banExpireTimestamp >= new DateTime()
        ) {
            throw new AccessDeniedException(
                'Je kunt tot ' . $this->userHelper->getUser()->banExpireTimestamp->format(DateGenerics::DATE_FORMAT) .
                ' geen gebruik maken van Somda'
            );
        }
    }

    /**
     * @param TerminateEvent $event
     * @throws Exception
     */
    public function onKernelTerminate(TerminateEvent $event): void
    {
        if (!$this->isShouldExecuteEventHandler($event)
            || $event->getResponse()->getStatusCode() !== Response::HTTP_OK
        ) {
            return;
        }

        if ($this->stopwatch->isStarted(self::STOPWATCH_NAME)) {
            $stopwatchEvent = $this->stopwatch->stop(self::STOPWATCH_NAME);

            $log = new Log();
            $log->user = $this->userHelper->getUser();
            $log->timestamp = new DateTime();
            $log->ipAddress = ip2long($event->getRequest()->getClientIp());
            $log->route = $this->route ?? '';
            $log->routeParameters = $this->routeParameters ?? [];
            $log->duration = $stopwatchEvent->getDuration() / 1000;
            $log->memoryUsage = floatval(sprintf('%+08.3f', $stopwatchEvent->getMemory())) / 1024 / 1024;
            $this->doctrine->getManager()->persist($log);
        }

        $this->saveVisit();

        $this->doctrine->getManager()->flush();
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        /**
         * @var User $user
         */
        $user = $event->getAuthenticationToken()->getUser();
        if (is_null($user->apiToken)) {
            // Generate an API token for this user
            $user->apiToken = uniqid('', true);
        }
        $user->apiTokenExpiryTimestamp = new DateTime(User::API_TOKEN_VALIDITY);
    }

    /**
     * @throws Exception
     */
    private function saveVisit(): void
    {
        if ($this->userHelper->userIsLoggedIn()) {
            $this->userHelper->getUser()->lastVisit = new DateTime();
        }
    }

    /**
     * @param KernelEvent $event
     * @return bool
     */
    private function isShouldExecuteEventHandler(KernelEvent $event): bool
    {
        if (!$event->isMasterRequest()) {
            return false;
        }

        $route = (string)$event->getRequest()->attributes->get('_route');
        return substr($route, 0, 1) !== '_'
            && substr($route, -5) !== '_json'
            && $route !== 'logout'
            && $route !== 'api_authenticate_token';
    }

    /**
     * @param KernelEvent $event
     * @return bool
     */
    private function isApiRequest(KernelEvent $event): bool
    {
        $route = (string)$event->getRequest()->attributes->get('_route');
        return substr($route, 0, 4) === 'api_';
    }
}
