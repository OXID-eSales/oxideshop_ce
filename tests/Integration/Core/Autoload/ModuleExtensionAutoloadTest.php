<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload;

use Error;
use OxidEsales\Eshop\Core\Module\ModuleVariablesLocator;
use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Core\Autoload\ModuleAutoload;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapter;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
final class ModuleExtensionAutoloadTest extends TestCase
{
    use ContainerTrait;

    private ShopControl $shopControl;

    protected function setUp(): void
    {
        parent::setUp();

        $_GET['searchparam'] = '';
        $_GET['page'] = '';
        $_GET['tpl'] = '';

        $this->createContainer();
        $this->disableShopEditionClassExtensionProtection();
        $this->compileContainer();

        $this->setupModuleFixture('controller_module');
        $this->setupModuleFixture('controller_extension_module');

        $this->attachContainerToContainerFactory();
        ModuleVariablesLocator::resetModuleVariables();

        $this->shopControl = new ShopControl();
    }

    protected function tearDown(): void
    {
        $this->uninstallModuleFixture('controller_extension_module');
        $this->uninstallModuleFixture('controller_module');

        parent::tearDown();
    }

    public function testRenderControllerExtendedByOtherModule(): void
    {
        ob_start();
        $this->shopControl->start('controller_module_greeting', '');
        $output = ob_get_clean();

        $this->assertStringContainsString('controller_module/start', $output);
    }

    public function testRenderControllerExtendingParentAlias(): void
    {
        ob_start();
        $this->shopControl->start('controller_module_start', '');
        $output = ob_get_clean();

        $this->assertStringContainsString('controller_module/start', $output);
    }

    public function testRenderControllerExtendingParentAliasWithoutModuleAutoload(): void
    {
        $this->assertTrue(spl_autoload_unregister([ModuleAutoload::class, 'autoload']));

        $this->expectException(Error::class);

        $this->shopControl->start('controller_module_start', '');
    }

    private function disableShopEditionClassExtensionProtection(): void
    {
        $shopAdapter = $this->createStub(ShopAdapter::class);
        $shopAdapter->method('isShopEditionNamespace')->willReturn(false);

        $this->container->set(ShopAdapterInterface::class, $shopAdapter);
        $this->container->autowire(ShopAdapterInterface::class, ShopAdapter::class);
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
        return __DIR__ . '/Fixtures';
    }
}
