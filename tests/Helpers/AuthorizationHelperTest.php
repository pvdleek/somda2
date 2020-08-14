<?php declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Entity\User;
use App\Helpers\AuthorizationHelper;
use App\Tests\BaseTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthorizationHelperTest extends BaseTestCase
{
    /**
     * @var AuthorizationHelper
     */
    private AuthorizationHelper $object;

    /**
     *
     */
    public function testGetUserNoToken(): void
    {
        $tokenStorageMock = $this->prophet->prophesize(TokenStorageInterface::class);
        $tokenStorageMock->getToken()->willReturn(null);

        $this->object = new AuthorizationHelper(
            $tokenStorageMock->reveal(),
            $this->prophet->prophesize(AuthorizationCheckerInterface::class)->reveal()
        );
        $this->assertNull($this->object->getUser());
    }

    /**
     *
     */
    public function testGetUserInvalidToken(): void
    {
        $tokenMock = $this->prophet->prophesize(TokenInterface::class);
        $tokenMock->getUser()->willReturn('fooBar');

        $tokenStorageMock = $this->prophet->prophesize(TokenStorageInterface::class);
        $tokenStorageMock->getToken()->willReturn($tokenMock->reveal());

        $this->object = new AuthorizationHelper(
            $tokenStorageMock->reveal(),
            $this->prophet->prophesize(AuthorizationCheckerInterface::class)->reveal()
        );
        $this->assertNull($this->object->getUser());
    }

    /**
     *
     */
    public function testGetUser(): void
    {
        $user = new User();
        $tokenMock = $this->prophet->prophesize(TokenInterface::class);
        $tokenMock->getUser()->willReturn($user);

        $tokenStorageMock = $this->prophet->prophesize(TokenStorageInterface::class);
        $tokenStorageMock->getToken()->willReturn($tokenMock->reveal());

        $this->object = new AuthorizationHelper(
            $tokenStorageMock->reveal(),
            $this->prophet->prophesize(AuthorizationCheckerInterface::class)->reveal()
        );
        $this->assertSame($user, $this->object->getUser());
    }
}
