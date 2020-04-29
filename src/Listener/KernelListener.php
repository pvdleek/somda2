<?php

namespace App\Listener;

use App\Entity\Log;
use App\Helpers\UserHelper;
use DateTime;
use Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelListener implements EventSubscriberInterface
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
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelView', 0]];
    }

    /**
     * @param RequestEvent $event
     * @throws Exception
     */
    public function onKernelView(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $route = (string)$event->getRequest()->attributes->get('_route');
        if (substr($route, 0, 1) === '_' || substr($route, -5) === '_json') {
            return;
        }

        $log = new Log();
        $log->user = $this->userHelper->getUser();
        $log->timestamp = new DateTime();
        $log->ipAddress = ip2long($event->getRequest()->getClientIp());
        $log->route = $route;
        $log->routeParameters = (array)$event->getRequest()->attributes->get('_route_params');

        $this->doctrine->getManager()->persist($log);
        $this->doctrine->getManager()->flush();
    }
}
