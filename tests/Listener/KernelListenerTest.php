<?php
declare(strict_types=1);

namespace App\Tests\Listener;

use App\Entity\User;
use App\Helpers\UserHelper;
use App\Listener\KernelListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class KernelListenerTest extends TestCase
{
    private KernelListener $listener;
    private ManagerRegistry $doctrine;
    private UserHelper $user_helper;
    private EntityManagerInterface $entity_manager;

    protected function setUp(): void
    {
        $this->doctrine = $this->createMock(ManagerRegistry::class);
        $this->user_helper = $this->createMock(UserHelper::class);
        $this->entity_manager = $this->createMock(EntityManagerInterface::class);
        
        $this->listener = new KernelListener($this->doctrine, $this->user_helper);
    }

    public function testGetSubscribedEvents(): void
    {
        $events = KernelListener::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::REQUEST, $events);
        $this->assertArrayHasKey(KernelEvents::TERMINATE, $events);
        $this->assertEquals(['onKernelRequest', -10], $events[KernelEvents::REQUEST]);
        $this->assertEquals(['onKernelTerminate', -10], $events[KernelEvents::TERMINATE]);
    }

    public function testOnKernelRequestWithNonMainRequest(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = new Request();
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::SUB_REQUEST);

        $this->user_helper
            ->expects($this->never())
            ->method('getUser');

        $this->listener->onKernelRequest($event);
        
        // Should not throw exception
        $this->assertTrue(true);
    }

    public function testOnKernelRequestWithNonBannedUser(): void
    {
        $user = $this->createMock(User::class);
        $user->ban_expire_timestamp = new \DateTime('-1 day');

        $this->user_helper
            ->method('getUser')
            ->willReturn($user);

        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = new Request();
        $request->attributes->set('_route', 'home');
        $request->attributes->set('_route_params', []);
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $this->listener->onKernelRequest($event);
        
        // Should not throw exception
        $this->assertTrue(true);
    }

    public function testOnKernelRequestWithBannedUserThrowsException(): void
    {
        $user = $this->createMock(User::class);
        $user->ban_expire_timestamp = new \DateTime('+1 day');

        $this->user_helper
            ->method('getUser')
            ->willReturn($user);

        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = new Request();
        $request->attributes->set('_route', 'home');
        $request->attributes->set('_route_params', []);
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $this->expectException(AccessDeniedException::class);

        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelRequestWithNoUser(): void
    {
        $this->user_helper
            ->method('getUser')
            ->willReturn(null);

        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = new Request();
        $request->attributes->set('_route', 'home');
        $request->attributes->set('_route_params', []);
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $this->listener->onKernelRequest($event);
        
        // Should not throw exception
        $this->assertTrue(true);
    }

    public function testOnKernelTerminateWithNonOkResponse(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = new Request();
        $request->attributes->set('_route', 'home');
        $response = new Response('', Response::HTTP_NOT_FOUND);
        $event = new TerminateEvent($kernel, $request, $response);

        $this->doctrine
            ->expects($this->never())
            ->method('getManager');

        $this->listener->onKernelTerminate($event);
    }

    public function testOnKernelTerminateWithUnderscoreRoute(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('http://localhost', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $request->attributes->set('_route', '_profiler');
        $response = new Response('', Response::HTTP_OK);
        $event = new TerminateEvent($kernel, $request, $response);

        // Routes starting with underscore should be ignored
        $this->doctrine
            ->expects($this->never())
            ->method('getManager');

        $this->listener->onKernelTerminate($event);
    }
}
