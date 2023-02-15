<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Application\Controller\Admin\ModuleConfiguration as ModuleConfigurationController;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use Psr\Container\ContainerInterface;

/**
 * @internal
 */
final class ModuleConfigurationTest extends TestCase
{
    private string $testModuleDir = 'testModule';
    private string $testModuleId = 'testModuleId';

    public function setUp(): void
    {
        $this->installTestModule();
        parent::setUp();
    }

    public function tearDown(): void
    {
        $this->uninstallTestModule();
        parent::tearDown();
    }

    public function testRender(): void
    {
        $_POST['oxid'] = $this->testModuleId;
        $moduleConfigurationController = oxNew(ModuleConfigurationController::class);

        $renderResponse = $moduleConfigurationController->render();
        $viewData = $moduleConfigurationController->getViewData();

        $this->assertEquals('module_config', $renderResponse);
        $this->assertSame(
            $this->testModuleId,
            $viewData['oModule']->getId()
        );
    }

    public function testSaveConfVarsForInactiveModule(): void
    {
        $this->saveConfVars(['stringSetting' => 'newValue']);
        ContainerFactory::resetContainer();

        $this->assertSame(
            'newValue',
            $this->getModuleConfiguration()->getModuleSettings()[0]->getValue()
        );
    }

    public function testSaveConfVarsForActiveModule(): void
    {
        $this->activateTestModule();

        $this->saveConfVars(['stringSetting' => 'newValue']);
        ContainerFactory::resetContainer();

        $this->assertSame(
            'newValue',
            $this->getModuleConfiguration()->getModuleSetting('stringSetting')->getValue()
        );
    }

    public function testModuleSettingCacheInvalidatedAfterSave(): void
    {
        $this->activateTestModule();

        $oldValueToBeCached = $this->getContainer()->get(ModuleSettingServiceInterface::class)
            ->getString('stringSetting', $this->testModuleId);
        $this->assertEquals('row', $oldValueToBeCached);

        $this->saveConfVars(['stringSetting' => 'newValue']);
        ContainerFactory::resetContainer();

        $this->assertSame(
            'newValue',
            $this->getContainer()->get(ModuleSettingServiceInterface::class)
                ->getString('stringSetting', $this->testModuleId)->toString()
        );
    }

    public function testSaveConfVarsSavesZeroNumAsInteger(): void
    {
        $this->activateTestModule();

        $this->saveConfVars(['testInt' => '0']);

        ContainerFactory::resetContainer();

        $this->assertSame(
            0,
            $this->getModuleConfiguration()->getModuleSetting('testInt')->getValue()
        );
    }

    public function testSaveConfVarsSavesNumAsInteger(): void
    {
        $this->activateTestModule();

        $this->saveConfVars(['testInt' => '321']);

        ContainerFactory::resetContainer();

        $this->assertSame(
            321,
            $this->getModuleConfiguration()->getModuleSetting('testInt')->getValue()
        );
    }

    public function testSaveConfVarsSavesZeroNumAsFloat(): void
    {
        $this->activateTestModule();

        $this->saveConfVars(['testFloat' => '0.0']);

        ContainerFactory::resetContainer();

        $this->assertSame(
            0.0,
            $this->getModuleConfiguration()->getModuleSetting('testFloat')->getValue()
        );
    }

    public function testSaveConfVarsSavesNumAsFloat(): void
    {
        $this->activateTestModule();

        $this->saveConfVars(['testFloat' => '123.321']);

        ContainerFactory::resetContainer();

        $this->assertSame(
            123.321,
            $this->getModuleConfiguration()->getModuleSetting('testFloat')->getValue()
        );
    }

    private function installTestModule(): void
    {
        $this->getContainer()->get(ModuleInstallerInterface::class)->install($this->getOxidEshopPackage());
    }

    private function activateTestModule(): void
    {
        $this->getContainer()->get(ModuleActivationBridgeInterface::class)->activate($this->testModuleId, 1);
    }

    private function getModuleConfiguration(): ModuleConfiguration
    {
        return $this->getContainer()->get(ModuleConfigurationDaoBridgeInterface::class)->get($this->testModuleId);
    }

    private function uninstallTestModule(): void
    {
        $this->getContainer()->get(ModuleInstallerInterface::class)->uninstall($this->getOxidEshopPackage());
    }

    private function getOxidEshopPackage(): OxidEshopPackage
    {
        return new OxidEshopPackage(__DIR__ . '/Fixtures/' . $this->testModuleDir);
    }

    private function getContainer(): ContainerInterface
    {
        return ContainerFactory::getInstance()->getContainer();
    }

    private function saveConfVars(array $confstrs = []): void
    {
        $_POST['oxid'] = $this->testModuleId;
        $_POST['confstrs'] = $confstrs;

        $moduleConfigurationController = oxNew(ModuleConfigurationController::class);
        $moduleConfigurationController->saveConfVars();
    }
}
