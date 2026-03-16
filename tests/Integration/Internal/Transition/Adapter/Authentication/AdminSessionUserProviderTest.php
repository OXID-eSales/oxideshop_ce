<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\Authentication;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\Exception\CsrfTokenMismatchException;
use OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\Exception\InsufficientAdminRightsException;
use OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\Exception\NoActiveSessionUserException;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Authentication\AdminSessionUserProvider;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

#[RunTestsInSeparateProcesses]
final class AdminSessionUserProviderTest extends TestCase
{
    private AdminSessionUserProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $context = $this->createStub(ContextInterface::class);
        $context->method('getCurrentShopId')->willReturn(1);
        $this->provider = new AdminSessionUserProvider($context);
    }

    public function testLoadsAdminMalladminWithRoleAdmin(): void
    {
        $userId = $this->createUser('admin@test.com', 'malladmin');

        $this->startSessionWithUser('auth', $userId, true);

        $user = $this->provider->loadSessionUser(
            new Request(cookies: ['admin_sid' => Registry::getSession()->getId()])
        );

        $this->assertSame('admin@test.com', $user->getUserIdentifier());
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_ADMIN_MALL', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());

        $this->deleteUser($userId);
    }

    public function testLoadsSubShopAdminWithRoleAdmin(): void
    {
        $userId = $this->createUser('shopadmin@test.com', '1');

        $this->startSessionWithUser('auth', $userId, true);

        $user = $this->provider->loadSessionUser(
            new Request(cookies: ['admin_sid' => Registry::getSession()->getId()])
        );

        $this->assertContains('ROLE_ADMIN', $user->getRoles());

        $this->deleteUser($userId);
    }

    public function testSubShopAdminForNonExistentShopThrowsAuthException(): void
    {
        $userId = $this->createUser('othershopadmin@test.com', '2');

        $this->startSessionWithUser('auth', $userId, true);

        $this->expectException(InsufficientAdminRightsException::class);

        try {
            $this->provider->loadSessionUser(
                new Request(cookies: ['admin_sid' => Registry::getSession()->getId()])
            );
        } finally {
            $this->deleteUser($userId);
        }
    }

    public function testAdminOfDifferentShopThrowsInsufficientRightsFromResolveRoles(): void
    {
        $context = $this->createStub(ContextInterface::class);
        $context->method('getCurrentShopId')->willReturn(99);
        $provider = new AdminSessionUserProvider($context);

        $userId = $this->createUser('wrongshop@test.com', '1');

        $this->startSessionWithUser('auth', $userId, true);

        $this->expectException(InsufficientAdminRightsException::class);

        try {
            $provider->loadSessionUser(
                new Request(cookies: ['admin_sid' => Registry::getSession()->getId()])
            );
        } finally {
            $this->deleteUser($userId);
        }
    }

    public function testCsrfMismatchThrowsCsrfTokenMismatchException(): void
    {
        $session = Registry::getSession();
        $session->setAdminMode(true);
        $session->start();
        $_GET['stoken'] = 'wrong-token';

        $this->expectException(CsrfTokenMismatchException::class);

        $this->provider->loadSessionUser(
            new Request(cookies: ['admin_sid' => $session->getId()])
        );
    }

    public function testLoginTokenMismatchThrowsNoActiveSessionUserException(): void
    {
        $userId = $this->createUser('logintoken@test.com', 'malladmin');

        $this->startSessionWithUser('auth', $userId, true);
        Registry::getSession()->setVariable('login-token', 'wrong-hash');

        $this->expectException(NoActiveSessionUserException::class);

        try {
            $this->provider->loadSessionUser(
                new Request(cookies: ['admin_sid' => Registry::getSession()->getId()])
            );
        } finally {
            $this->deleteUser($userId);
        }
    }

    public function testAdminSidCookieWithoutAuthSessionVarIsNotAdmin(): void
    {
        $session = Registry::getSession();
        $session->setAdminMode(true);
        $session->start();
        $_GET['stoken'] = $session->getSessionChallengeToken();

        $this->expectException(InsufficientAdminRightsException::class);

        $this->provider->loadSessionUser(
            new Request(cookies: ['admin_sid' => $session->getId()])
        );
    }

    public function testAdminWithBothCookiesAuthenticatesAsAdmin(): void
    {
        $userId = $this->createUser('admin-both@test.com', 'malladmin');

        $this->startSessionWithUser('auth', $userId, true);

        $user = $this->provider->loadSessionUser(
            new Request(
                cookies: [
                    'admin_sid' => Registry::getSession()->getId(),
                    'sid' => 'some-frontend-session-id',
                ]
            )
        );

        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_ADMIN_MALL', $user->getRoles());

        $this->deleteUser($userId);
    }

    public function testThrowsOnInsufficientAdminRights(): void
    {
        $userId = $this->createUser('noadmin@test.com', 'user');

        $this->startSessionWithUser('auth', $userId, true);

        $this->expectException(InsufficientAdminRightsException::class);

        try {
            $this->provider->loadSessionUser(
                new Request(cookies: ['admin_sid' => Registry::getSession()->getId()])
            );
        } finally {
            $this->deleteUser($userId);
        }
    }

    private function startSessionWithUser(string $key, string $userId, bool $adminMode = false): void
    {
        $session = Registry::getSession();
        $session->setAdminMode($adminMode);
        $session->start();
        $session->setVariable($key, $userId);
        $_GET['stoken'] = $session->getSessionChallengeToken();
    }

    private function createUser(string $username, string $rights): string
    {
        $user = new class extends User {
            public string $desiredRights = 'user';

            protected function getUserRights(): string
            {
                return $this->desiredRights;
            }
        };
        $user->desiredRights = $rights;
        $user->assign([
            'oxusername' => $username,
            'oxactive' => 1,
            'oxshopid' => 1,
            'oxregister' => date('Y-m-d H:i:s'),
        ]);
        $user->setPassword('TestPassword123');
        $user->save();

        return $user->getId();
    }

    private function deleteUser(string $userId): void
    {
        $user = oxNew(User::class);
        $user->delete($userId);
    }
}
