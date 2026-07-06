<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Controller;

use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
final class ModuleControllerRenderTest extends IntegrationTestCase
{
	private ShopControl $shopControl;

	public function setUp(): void
    {
        parent::setUp();

	    $_GET['searchparam'] = '';
	    $_GET['page'] = '';
	    $_GET['tpl'] = '';

        $this->setupModuleFixture('module1');

        $this->shopControl = new ShopControl();
    }

    public function tearDown(): void
    {
        $this->uninstallModuleFixture('module1');

        parent::tearDown();
    }

    public function testRenderTraditionalController(): void
    {
        $output = $this->shopControl->buildResponse('module1_controller', '')->getContent();

        $this->assertStringContainsString('module1/module_controller', $output);
    }

    public function testRenderServiceController(): void
    {
        $output = $this->shopControl->buildResponse('test_module_controller_as_service', '')->getContent();

        $this->assertStringContainsString('module1/module_controller_as_service', $output);
    }

    public function testRenderServiceControllerWithFunction(): void
    {
        $output = $this->buildResponseCapturingEchoedOutput('test_module_controller_as_service', 'testFunction');

        $this->assertStringContainsString('module1/module_controller_as_service', $output);
        $this->assertStringContainsString('Function output', $output);
    }

    public function testControllerDecorator(): void
    {
        $output = $this->buildResponseCapturingEchoedOutput('test_module_controller_as_service', 'testFunction');

        $this->assertStringContainsString('module1/module_controller_as_service', $output);
        $this->assertStringContainsString('Init Decorator', $output);
    }

    private function buildResponseCapturingEchoedOutput(string $controllerKey, string $function): string
    {
        ob_start();
        $response = $this->shopControl->buildResponse($controllerKey, $function);

        return ob_get_clean() . $response->getContent();
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
