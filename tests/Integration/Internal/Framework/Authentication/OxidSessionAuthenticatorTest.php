<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Authentication;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
final class OxidSessionAuthenticatorTest extends TestCase
{
    private string $testUserId;
    private string $testUsername = 'session-test@example.com';
    private string $testAdminId;
    private string $testAdminUsername = 'session-admin@example.com';
    private string $shopUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shopUrl = Registry::getConfig()->getShopUrl();

        $this->setupModule();
        $this->testUserId = $this->createUser($this->testUsername, 'user');
        $this->testAdminId = $this->createUser($this->testAdminUsername, 'malladmin');
    }

    protected function tearDown(): void
    {
        $this->deleteUser($this->testUserId);
        $this->deleteUser($this->testAdminId);
        $this->uninstallModule();

        parent::tearDown();
    }

    public function testAuthenticatedFrontendUserGets200(): void
    {
        $login = $this->login($this->testUserId, false);

        $response = $this->get('api/test/session-auth?stoken=' . $login['stoken'], [
            $login['session_name'] => $login['session_id'],
        ]);

        $this->assertSame(200, $response['status'], $response['body']);
        $data = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);
        $this->assertTrue($data['authenticated']);
        $this->assertSame($this->testUsername, $data['username']);
    }

    public function testAdminWithAdminSidOnFrontendRouteGets401(): void
    {
        $login = $this->login($this->testAdminId, true);

        $response = $this->get('api/test/session-auth?stoken=' . $login['stoken'], [
            $login['session_name'] => $login['session_id'],
        ]);

        $this->assertSame(401, $response['status']);
        $data = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Authentication required', $data['error']);
    }

    public function testNoCookieGets401(): void
    {
        $response = $this->get('api/test/session-auth');

        $this->assertSame(401, $response['status']);
        $data = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Authentication required', $data['error']);
    }

    public function testInvalidSessionGets401(): void
    {
        $response = $this->get('api/test/session-auth', ['sid' => 'nonexistent-session-id']);

        $this->assertSame(401, $response['status']);
        $data = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Authentication required', $data['error']);
    }

    public function testRegularUserOnAdminEndpointGets401(): void
    {
        $login = $this->login($this->testUserId, false);

        $response = $this->get('api/test/session-admin?stoken=' . $login['stoken'], [
            $login['session_name'] => $login['session_id'],
        ]);

        $this->assertSame(401, $response['status']);
        $data = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Authentication required', $data['error']);
    }

    public function testAdminOnAdminEndpointGets200(): void
    {
        $login = $this->login($this->testAdminId, true);

        $response = $this->get('api/test/session-admin?stoken=' . $login['stoken'], [
            $login['session_name'] => $login['session_id'],
        ]);

        $this->assertSame(200, $response['status'], $response['body']);
        $data = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);
        $this->assertTrue($data['authenticated']);
        $this->assertContains('ROLE_ADMIN', $data['roles']);
    }

    public function testAdminViaFrontendSessionOnAdminEndpointGets401(): void
    {
        $login = $this->login($this->testAdminId, false);

        $response = $this->get('api/test/session-admin?stoken=' . $login['stoken'], [
            $login['session_name'] => $login['session_id'],
        ]);

        $this->assertSame(401, $response['status']);
        $data = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Authentication required', $data['error']);
    }

    public function testXhrWithoutCsrfTokenGets401(): void
    {
        $login = $this->login($this->testUserId, false);

        $response = $this->get('api/test/session-auth', [
            $login['session_name'] => $login['session_id'],
        ], ['X-Requested-With: XMLHttpRequest']);

        $this->assertSame(401, $response['status']);
        $data = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Authentication required', $data['error']);
    }

    public function testXhrWithValidCsrfTokenGets200(): void
    {
        $login = $this->login($this->testUserId, false);

        $response = $this->get('api/test/session-auth?stoken=' . $login['stoken'], [
            $login['session_name'] => $login['session_id'],
        ], ['X-Requested-With: XMLHttpRequest']);

        $this->assertSame(200, $response['status'], $response['body']);
        $data = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);
        $this->assertTrue($data['authenticated']);
    }

    public function testFrontendAndAdminSessionsCoexist(): void
    {
        $frontendLogin = $this->login($this->testUserId, false);
        $adminLogin = $this->login($this->testAdminId, true);

        $cookies = [
            'sid' => $frontendLogin['session_id'],
            'admin_sid' => $adminLogin['session_id'],
        ];

        $response = $this->get('api/test/session-auth?stoken=' . $frontendLogin['stoken'], $cookies);
        $this->assertSame(200, $response['status'], $response['body']);
        $data = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame($this->testUsername, $data['username']);

        $response = $this->get('api/test/session-admin?stoken=' . $adminLogin['stoken'], $cookies);
        $this->assertSame(200, $response['status'], $response['body']);
        $data = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame($this->testAdminUsername, $data['username']);
    }

    /** @param string[] $cookies @param string[] $headers @return array{status: int, body: string} */
    private function get(string $path, array $cookies = [], array $headers = []): array
    {
        $ch = curl_init($this->shopUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIE => implode('; ', array_map(
                static fn(string $name, string $value): string => "$name=$value",
                array_keys($cookies),
                array_values($cookies),
            )),
            CURLOPT_HTTPHEADER => $headers,
        ]);
        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['status' => $status, 'body' => $body];
    }

    /** @return array{session_name: string, session_id: string, stoken: string} */
    private function login(string $userId, bool $isAdmin): array
    {
        $session = Registry::getSession();

        if ($session->isSessionStarted()) {
            session_write_close();
        }

        session_id(bin2hex(random_bytes(16)));

        $session->setAdminMode($isAdmin);
        $session->setForceNewSession();
        $session->start();
        $session->setVariable($isAdmin ? 'auth' : 'usr', $userId);

        $stoken = $session->getSessionChallengeToken();
        $sessionId = $session->getId();

        session_write_close();

        return [
            'session_name' => $isAdmin ? 'admin_sid' : 'sid',
            'session_id' => $sessionId,
            'stoken' => $stoken,
        ];
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
        $user->save();

        return $user->getId();
    }

    private function deleteUser(string $userId): void
    {
        $user = oxNew(User::class);
        $user->delete($userId);
    }

    private function setupModule(): void
    {
        $shopId = ContainerFacade::get(BasicContextInterface::class)->getDefaultShopId();

        ContainerFacade::get(ModuleInstallerInterface::class)
            ->install(new OxidEshopPackage(__DIR__ . '/Fixtures/testModule/'));

        ContainerFacade::get(ModuleActivationBridgeInterface::class)
            ->activate('session_auth_test', $shopId);
    }

    private function uninstallModule(): void
    {
        ContainerFacade::get(ModuleInstallerInterface::class)
            ->uninstall(new OxidEshopPackage(__DIR__ . '/Fixtures/testModule/'));
    }
}
