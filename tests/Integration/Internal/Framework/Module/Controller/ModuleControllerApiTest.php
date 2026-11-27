<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Controller;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\Eshop\Core\Registry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;

#[RunTestsInSeparateProcesses]
final class ModuleControllerApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setupModuleFixture('module1');
    }

    protected function tearDown(): void
    {
        $this->uninstallModuleFixture('module1');

        parent::tearDown();
    }

    public function testApiController(): void
    {
        $response = $this->request('GET', 'api/oxid/8/');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            [
                'name' => 'oxid',
                'id' => 8,
                'configParameter' => 'hello',
            ],
            \json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR)
        );
    }

    public function testApiControllerWrongRequest(): void
    {
        $response = $this->request('GET', 'api/oxid/notInt/');

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('application/json', $response->getHeader('Content-Type'));
    }

    public function testApiRouteAnswersPostWithMethodNotAllowed(): void
    {
        $response = $this->request('POST', 'api/oxid/8/');

        $this->assertSame(405, $response->getStatusCode());
        $this->assertSame('GET', $response->getHeader('Allow'));
    }

    public function testApiScriptServesApiRoutes(): void
    {
        $response = $this->request('GET', 'api.php/api/oxid/8/');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('oxid', json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR)['name']);
    }

    public function testApiScriptDoesNotServeStorefront(): void
    {
        $this->assertSame(404, $this->request('GET', 'api.php?cl=start')->getStatusCode());
    }

    public static function entryPointDataProvider(): array
    {
        return [
            'admin' => ['admin/index.php/api/oxid/8/'],
            'widget' => ['widget.php/api/oxid/8/?cl=oxwminibasket'],
        ];
    }

    #[DataProvider('entryPointDataProvider')]
    public function testOtherEntryPointsDoNotServeModuleRoutes(string $path): void
    {
        $response = $this->request('GET', $path);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertStringNotContainsString('json', (string) $response->getHeader('Content-Type'));
        $this->assertStringNotContainsString('"name":"oxid"', $response->getContent());
    }

    private function request(string $method, string $path): BrowserKitResponse
    {
        $httpBrowser = new HttpBrowser();
        $httpBrowser->request($method, Registry::getConfig()->getShopUrl() . $path);

        return $httpBrowser->getResponse();
    }

    private function get(string $serviceId)
    {
        return ContainerFacade::get($serviceId);
    }

    private function setupModuleFixture(string $moduleId): void
    {
        $this->installModuleFixture($moduleId);
        $this->activateModuleFixture($moduleId);
    }

    private function installModuleFixture(string $moduleId): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->install($this->getPackageFixture($moduleId));
    }

    private function activateModuleFixture(string $moduleId): void
    {
        $this->get(ModuleActivationBridgeInterface::class)
            ->activate($moduleId, $this->get(BasicContextInterface::class)->getDefaultShopId());
    }

    private function uninstallModuleFixture(string $moduleId): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->uninstall($this->getPackageFixture($moduleId));
    }

    private function getPackageFixture(string $moduleId): OxidEshopPackage
    {
        return new OxidEshopPackage("{$this->getFixturesDirectory()}/$moduleId/");
    }

    private function getFixturesDirectory(): string
    {
        return __DIR__ . "/Fixtures";
    }
}
