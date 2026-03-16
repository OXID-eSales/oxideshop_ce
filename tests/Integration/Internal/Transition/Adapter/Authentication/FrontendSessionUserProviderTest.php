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
use OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\Exception\NoActiveSessionUserException;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Authentication\FrontendSessionUserProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

#[RunTestsInSeparateProcesses]
final class FrontendSessionUserProviderTest extends TestCase
{
    private FrontendSessionUserProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new FrontendSessionUserProvider();
    }

    public function testLoadsFrontendUserWithRoleUser(): void
    {
        $userId = $this->createUser('frontend@test.com', 'user');

        $this->startSessionWithUser('usr', $userId);

        $user = $this->provider->loadSessionUser(
            new Request(cookies: ['sid' => Registry::getSession()->getId()])
        );

        $this->assertSame('frontend@test.com', $user->getUserIdentifier());
        $this->assertSame(['ROLE_USER'], $user->getRoles());

        $this->deleteUser($userId);
    }

    public function testAdminUserOnFrontendSessionGetsOnlyRoleUser(): void
    {
        $userId = $this->createUser('admin-frontend@test.com', 'malladmin');

        $this->startSessionWithUser('usr', $userId);

        $user = $this->provider->loadSessionUser(
            new Request(cookies: ['sid' => Registry::getSession()->getId()])
        );

        $this->assertSame(['ROLE_USER'], $user->getRoles());

        $this->deleteUser($userId);
    }

    public function testThrowsOnCsrfMismatch(): void
    {
        $userId = $this->createUser('csrf@test.com', 'user');

        $session = Registry::getSession();
        $session->start();
        $session->setVariable('usr', $userId);
        $_GET['stoken'] = 'wrong-token';

        $this->expectException(CsrfTokenMismatchException::class);

        try {
            $this->provider->loadSessionUser(
                new Request(cookies: ['sid' => $session->getId()])
            );
        } finally {
            $this->deleteUser($userId);
        }
    }

    public function testThrowsWhenNoActiveUser(): void
    {
        $session = Registry::getSession();
        $session->start();
        $_GET['stoken'] = $session->getSessionChallengeToken();

        $this->expectException(NoActiveSessionUserException::class);

        $this->provider->loadSessionUser(
            new Request(cookies: ['sid' => $session->getId()])
        );
    }

    private function startSessionWithUser(string $key, string $userId): void
    {
        $session = Registry::getSession();
        $session->setAdminMode(false);
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
