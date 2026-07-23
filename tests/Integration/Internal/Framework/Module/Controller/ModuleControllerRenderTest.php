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
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Filesystem\Path;

#[RunTestsInSeparateProcesses]
final class ModuleControllerRenderTest extends IntegrationTestCase
{
	private ShopControl $shopControl;

	public function setUp(): void
    {
        parent::setUp();

        $themePath = __DIR__ . '/Fixtures/testTheme';
        $shopRootPath = $this->get(BasicContextInterface::class)->getShopRootPath();
        $configuration = (new ThemeConfiguration())
            ->setId('testTheme')
            ->setSource(Path::makeRelative($themePath, $shopRootPath))
            ->setActivated(true);
        $configuration->addThemeSetting(
            (new Setting())->setName('defaultListDisplayType')->setType('str')->setValue('infogrid')
        );
        $configuration->addThemeSetting(
            (new Setting())->setName('numberOfCategoryProducts')->setType('arr')->setValue(['10', '20', '50', '100'])
        );
        $configuration->addThemeSetting(
            (new Setting())->setName('showVouchers')->setType('bool')->setValue(true)
        );
        $shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, $shopId);

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
