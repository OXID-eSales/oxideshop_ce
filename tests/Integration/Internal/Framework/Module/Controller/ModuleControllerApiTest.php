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
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;

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
        $shopUrl = ContainerFacade::getParameter('oxid_esales.shop_url');
        $httpBrowser = new HttpBrowser();
        $httpBrowser->request('GET', $shopUrl . 'api/oxid/8/');
        $response = $httpBrowser->getResponse();

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
        $shopUrl = ContainerFacade::getParameter('oxid_esales.shop_url');
        $httpBrowser = new HttpBrowser();
        $httpBrowser->request('GET', $shopUrl . 'api/oxid/notInt/');
        $response = $httpBrowser->getResponse();

        $this->assertSame(500, $response->getStatusCode());
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
