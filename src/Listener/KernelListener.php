<?php

declare(strict_types=1);

namespace App\Listener;

use App\Entity\Log;
use App\Entity\User;
use App\Generics\DateGenerics;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
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

    private ?Stopwatch $stopwatch;

    private ?string $route;

    private ?array $route_parameters;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $user_helper,
    ) {
        $this->stopwatch = new Stopwatch(true);
        $this->stopwatch->start(self::STOPWATCH_NAME);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', -10],
            KernelEvents::REQUEST => ['onKernelRequest', -10],
            KernelEvents::TERMINATE => ['onKernelTerminate', -10],
            SecurityEvents::INTERACTIVE_LOGIN => ['onInteractiveLogin', -10],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if ($this->isApiRequest($event)) {
            $event->setResponse(new Response('{ "error": "' . $event->getThrowable()->getMessage() . '" }'));
        }
    }

    /**
     * @throws \Exception
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$this->isShouldExecuteEventHandler($event)) {
            return;
        }

        $this->route = (string) $event->getRequest()->attributes->get('_route');
        $this->route_parameters = (array) $event->getRequest()->attributes->get('_route_params');

        if ($this->isApiRequest($event)
            && $event->getRequest()->headers->has(UserHelper::KEY_API_USER_ID)
            && $event->getRequest()->headers->has(UserHelper::KEY_API_TOKEN)
        ) {
            $this->user_helper->setFromApiRequest(
                (int) $event->getRequest()->headers->get(UserHelper::KEY_API_USER_ID),
                $event->getRequest()->headers->get(UserHelper::KEY_API_TOKEN)
            );
        }

        if (null !== $this->user_helper->getUser()
            && $this->user_helper->getUser()->ban_expire_timestamp >= new \DateTime()
        ) {
            throw new AccessDeniedException(
                'Je kunt tot '.$this->user_helper->getUser()->ban_expire_timestamp->format(DateGenerics::DATE_FORMAT).' geen gebruik maken van Somda'
            );
        }
    }

    /**
     * @throws \Exception
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
            $log->user = $this->user_helper->getUser();
            $log->timestamp = new \DateTime();
            $log->ip_address = \ip2long($event->getRequest()->getClientIp());
            $log->route = $this->route ?? '';
            $log->route_parameters = $this->route_parameters ?? [];
            $log->duration = $stopwatchEvent->getDuration() / 1000;
            $log->memory_usage = \floatval(\sprintf('%+08.3f', $stopwatchEvent->getMemory())) / 1024 / 1024;
            $this->doctrine->getManager()->persist($log);
        }

        $this->saveVisit();

        $this->doctrine->getManager()->flush();
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        /**
         * @var User $user
         */
        $user = $event->getAuthenticationToken()->getUser();
        if (null === $user->api_token) {
            // Generate an API token for this user
            $user->api_token = \uniqid('', true);
        }
        $user->api_token_expiry_timestamp = new \DateTime(User::API_TOKEN_VALIDITY);
    }

    /**
     * @throws \Exception
     */
    private function saveVisit(): void
    {
        if ($this->user_helper->userIsLoggedIn()) {
            $this->user_helper->getUser()->last_visit = new \DateTime();
        }
    }

    private function isShouldExecuteEventHandler(KernelEvent $event): bool
    {
        if (!$event->isMainRequest()) {
            return false;
        }

        $route = (string) $event->getRequest()->attributes->get('_route');
        return \substr($route, 0, 1) !== '_'
            && \substr($route, -5) !== '_json'
            && $route !== 'logout'
            && $route !== 'api_authenticate_token';
    }

    private function isApiRequest(KernelEvent $event): bool
    {
        $route = (string) $event->getRequest()->attributes->get('_route');
        return \substr($route, 0, 4) === 'api_';
    }
}
