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
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\HttpBrowser;

#[RunTestsInSeparateProcesses]
final class OxidSessionAuthenticatorTest extends TestCase
{
    private string $testUserId;
    private string $testUsername = 'session-test@example.com';
    private string $testPassword = 'TestPassword123';
    private string $testAdminId;
    private string $testAdminUsername = 'session-admin@example.com';
    private string $testAdminPassword = 'AdminPassword123';
    private string $shopUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shopUrl = Registry::getConfig()->getShopUrl();

        $this->setupModule();
        $this->testUserId = $this->createUser($this->testUsername, $this->testPassword, 'user');
        $this->testAdminId = $this->createUser($this->testAdminUsername, $this->testAdminPassword, 'malladmin');
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
        $browser = new HttpBrowser();

        $login = $this->loginViaApi($browser, $this->testUserId, false);

        $browser->getCookieJar()->set(new Cookie($login['session_name'], $login['session_id']));
        $browser->request('GET', $this->shopUrl . 'api/test/session-auth?stoken=' . $login['stoken']);

        $response = $browser->getResponse();
        $this->assertSame(200, $response->getStatusCode(), $response->getContent());

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertTrue($data['authenticated']);
        $this->assertSame($this->testUsername, $data['username']);
    }

    public function testAuthenticatedAdminGets200(): void
    {
        $browser = new HttpBrowser();

        $login = $this->loginViaApi($browser, $this->testAdminId, true);

        $browser->getCookieJar()->set(new Cookie($login['session_name'], $login['session_id']));
        $browser->request('GET', $this->shopUrl . 'api/test/session-auth?stoken=' . $login['stoken']);

        $response = $browser->getResponse();
        $this->assertSame(200, $response->getStatusCode(), $response->getContent());

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertTrue($data['authenticated']);
        $this->assertSame($this->testAdminUsername, $data['username']);
    }

    public function testNoCookieGets401(): void
    {
        $browser = new HttpBrowser();
        $browser->request('GET', $this->shopUrl . 'api/test/session-auth');

        $response = $browser->getResponse();
        $this->assertSame(401, $response->getStatusCode());

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Authentication required', $data['error']);
    }

    public function testInvalidSessionGets401(): void
    {
        $browser = new HttpBrowser();
        $browser->getCookieJar()->set(new Cookie('sid', 'nonexistent-session-id'));
        $browser->request('GET', $this->shopUrl . 'api/test/session-auth');

        $response = $browser->getResponse();
        $this->assertSame(401, $response->getStatusCode());

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Authentication required', $data['error']);
    }

    public function testRegularUserOnAdminEndpointGets403(): void
    {
        $browser = new HttpBrowser();

        $login = $this->loginViaApi($browser, $this->testUserId, false);

        $browser->getCookieJar()->set(new Cookie($login['session_name'], $login['session_id']));
        $browser->request('GET', $this->shopUrl . 'api/test/session-admin?stoken=' . $login['stoken']);

        $response = $browser->getResponse();
        $this->assertSame(403, $response->getStatusCode());

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Access denied', $data['error']);
    }

    public function testAdminOnAdminEndpointGets200(): void
    {
        $browser = new HttpBrowser();

        $login = $this->loginViaApi($browser, $this->testAdminId, true);

        $browser->getCookieJar()->set(new Cookie($login['session_name'], $login['session_id']));
        $browser->request('GET', $this->shopUrl . 'api/test/session-admin?stoken=' . $login['stoken']);

        $response = $browser->getResponse();
        $this->assertSame(200, $response->getStatusCode(), $response->getContent());

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertTrue($data['authenticated']);
        $this->assertContains('ROLE_ADMIN', $data['roles']);
    }

    public function testAdminViaFrontendSessionOnAdminEndpointGets403(): void
    {
        $browser = new HttpBrowser();

        $login = $this->loginViaApi($browser, $this->testAdminId, false);

        $browser->getCookieJar()->set(new Cookie($login['session_name'], $login['session_id']));
        $browser->request('GET', $this->shopUrl . 'api/test/session-admin?stoken=' . $login['stoken']);

        $response = $browser->getResponse();
        $this->assertSame(403, $response->getStatusCode());

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Access denied', $data['error']);
    }

    public function testXhrWithoutCsrfTokenGets401(): void
    {
        $browser = new HttpBrowser();

        $login = $this->loginViaApi($browser, $this->testUserId, false);

        $browser->getCookieJar()->set(new Cookie($login['session_name'], $login['session_id']));
        $browser->request('GET', $this->shopUrl . 'api/test/session-auth', [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $browser->getResponse();
        $this->assertSame(401, $response->getStatusCode());

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Authentication required', $data['error']);
    }

    public function testXhrWithValidCsrfTokenGets200(): void
    {
        $browser = new HttpBrowser();

        $login = $this->loginViaApi($browser, $this->testUserId, false);

        $browser->getCookieJar()->set(new Cookie($login['session_name'], $login['session_id']));
        $browser->request('GET', $this->shopUrl . 'api/test/session-auth?stoken=' . $login['stoken'], [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $browser->getResponse();
        $this->assertSame(200, $response->getStatusCode(), $response->getContent());

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertTrue($data['authenticated']);
    }

    private function loginViaApi(HttpBrowser $browser, string $userId, bool $isAdmin): array
    {
        $browser->request('POST', $this->shopUrl . 'api/test/session-login', [
            'user_id' => $userId,
            'is_admin' => $isAdmin ? '1' : '0',
        ]);

        $response = $browser->getResponse();
        $this->assertSame(200, $response->getStatusCode(), $response->getContent());

        return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    private function createUser(string $username, string $password, string $rights): string
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
        ]);
        $user->setPassword($password);
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
        ContainerFacade::get(ModuleInstallerInterface::class)
            ->install(new OxidEshopPackage(__DIR__ . '/Fixtures/testModule/'));

        ContainerFacade::get(ModuleActivationBridgeInterface::class)
            ->activate('session_auth_test', ContainerFacade::get(BasicContextInterface::class)->getDefaultShopId());
    }

    private function uninstallModule(): void
    {
        ContainerFacade::get(ModuleInstallerInterface::class)
            ->uninstall(new OxidEshopPackage(__DIR__ . '/Fixtures/testModule/'));
    }
}
