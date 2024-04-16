<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Application\Controller\Admin\ModuleConfiguration as ModuleConfigurationController;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

/**
 * @internal
 */
final class ModuleConfigurationTest extends IntegrationTestCase
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
        $this->unsetAdminMode();
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

        $oldValueToBeCached = ContainerFacade::get(ModuleSettingServiceInterface::class)
            ->getString('stringSetting', $this->testModuleId);
        $this->assertEquals('row', $oldValueToBeCached);

        $this->saveConfVars(['stringSetting' => 'newValue']);
        ContainerFactory::resetContainer();

        $this->assertSame(
            'newValue',
            ContainerFacade::get(ModuleSettingServiceInterface::class)
                ->getString('stringSetting', $this->testModuleId)
                ->toString()
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
        ContainerFacade::get(ModuleInstallerInterface::class)
            ->install($this->getOxidEshopPackage());
    }

    private function activateTestModule(): void
    {
        ContainerFacade::get(ModuleActivationBridgeInterface::class)
            ->activate($this->testModuleId, 1);
    }

    private function getModuleConfiguration(): ModuleConfiguration
    {
        return ContainerFacade::get(ModuleConfigurationDaoBridgeInterface::class)
            ->get($this->testModuleId);
    }

    private function uninstallTestModule(): void
    {
        ContainerFacade::get(ModuleInstallerInterface::class)
            ->uninstall($this->getOxidEshopPackage());
    }

    private function getOxidEshopPackage(): OxidEshopPackage
    {
        return new OxidEshopPackage(__DIR__ . '/Fixtures/' . $this->testModuleDir);
    }

    private function saveConfVars(array $confstrs = []): void
    {
        $_POST['oxid'] = $this->testModuleId;
        $_POST['confstrs'] = $confstrs;

        $moduleConfigurationController = oxNew(ModuleConfigurationController::class);
        $moduleConfigurationController->saveConfVars();
    }

    private function unsetAdminMode(): void
    {
        Registry::getSession()->setAdminMode(false);
        Registry::getConfig()->setAdminMode(false);
    }
}
