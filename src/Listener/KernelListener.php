<?php

declare(strict_types=1);

namespace App\Listener;

use App\Entity\Log;
use App\Generics\DateGenerics;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\KernelEvents;
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
            KernelEvents::REQUEST => ['onKernelRequest', -10],
            KernelEvents::TERMINATE => ['onKernelTerminate', -10],
        ];
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
            $stopwatch_event = $this->stopwatch->stop(self::STOPWATCH_NAME);

            $log = new Log();
            $log->user = $this->user_helper->getUser();
            $log->timestamp = new \DateTime();
            $log->ip_address = \ip2long($event->getRequest()->getClientIp());
            $log->route = $this->route ?? '';
            $log->route_parameters = $this->route_parameters ?? [];
            $log->duration = $stopwatch_event->getDuration() / 1000;
            $log->memory_usage = \floatval(\sprintf('%+08.3f', $stopwatch_event->getMemory())) / 1024 / 1024;
            $this->doctrine->getManager()->persist($log);
        }

        $this->saveVisit();

        $this->doctrine->getManager()->flush();
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

        return \substr($route, 0, 1) !== '_' && \substr($route, -5) !== '_json' && $route !== 'logout';
    }
}
