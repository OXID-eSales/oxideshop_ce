<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Controller;

use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
final class ModuleControllerRenderTest extends TestCase
{
	private ShopControl $shopControl;

	protected function setUp(): void
    {
        parent::setUp();

	    $_GET['searchparam'] = '';
	    $_GET['page'] = '';
	    $_GET['tpl'] = '';

        $this->setupModuleFixture('module1');

        $this->shopControl = new ShopControl();
    }

    protected function tearDown(): void
    {
        $this->uninstallModuleFixture('module1');

        parent::tearDown();
    }

    public function testRenderTraditionalController(): void
    {
        ob_start();
	    $this->shopControl->start('module1_controller', '');
	    $output = ob_get_clean();

	    $this->assertStringContainsString('module1/module_controller', $output);
    }

    public function testRenderServiceController(): void
    {
        ob_start();
        $this->shopControl->start('test_module_controller_as_service', '');
        $output = ob_get_clean();

        $this->assertStringContainsString('module1/module_controller_as_service', $output);
    }

    public function testRenderServiceControllerWithFunction(): void
    {
        ob_start();
        $this->shopControl->start('test_module_controller_as_service', 'testFunction');
        $output = ob_get_clean();

        $this->assertStringContainsString('module1/module_controller_as_service', $output);
        $this->assertStringContainsString('Function output', $output);
    }

    public function testControllerDecorator(): void
    {
        ob_start();
        $this->shopControl->start('test_module_controller_as_service', 'testFunction');
        $output = ob_get_clean();

        $this->assertStringContainsString('module1/module_controller_as_service', $output);
        $this->assertStringContainsString('Init Decorator', $output);
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
