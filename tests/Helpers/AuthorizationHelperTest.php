<?php
declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Helpers\AuthorizationHelper;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthorizationHelperTest extends TestCase
{
    private AuthorizationHelper $authorization_helper;
    private TokenStorageInterface $token_storage;
    private AuthorizationCheckerInterface $security_checker;

    protected function setUp(): void
    {
        $this->token_storage = $this->createMock(TokenStorageInterface::class);
        $this->security_checker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->authorization_helper = new AuthorizationHelper(
            $this->token_storage,
            $this->security_checker
        );
    }

    public function testGetUserReturnsNullWhenNoToken(): void
    {
        $this->token_storage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $result = $this->authorization_helper->getUser();

        $this->assertNull($result);
    }

    public function testGetUserReturnsUserWhenAuthenticated(): void
    {
        $user = $this->createMock(User::class);
        
        $token = $this->createMock(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->token_storage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $result = $this->authorization_helper->getUser();

        $this->assertInstanceOf(User::class, $result);
    }

    public function testIsGrantedReturnsTrueWhenRoleGranted(): void
    {
        $user = $this->createMock(User::class);
        
        $token = $this->createMock(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->token_storage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $this->security_checker
            ->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_ADMIN', $user)
            ->willReturn(true);

        $result = $this->authorization_helper->isGranted('ROLE_ADMIN');

        $this->assertTrue($result);
    }

    public function testIsGrantedReturnsFalseWhenRoleNotGranted(): void
    {
        $user = $this->createMock(User::class);
        
        $token = $this->createMock(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->token_storage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $this->security_checker
            ->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_ADMIN', $user)
            ->willReturn(false);

        $result = $this->authorization_helper->isGranted('ROLE_ADMIN');

        $this->assertFalse($result);
    }
}
