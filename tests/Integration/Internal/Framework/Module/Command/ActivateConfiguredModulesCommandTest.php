<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\TestingLibrary\Services\Library\DatabaseRestorer\DatabaseRestorer;
use Webmozart\PathUtil\Path;

/**
 * @internal
 */
final class ActivateConfiguredModulesCommandTest extends ModuleCommandsTestCase
{
    private $commandName = "oe:module:apply-configuration";
    /**
     * @var DatabaseRestorer
     */
    private $databaseRestorer;

    public function setup(): void
    {
        $this->databaseRestorer = new DatabaseRestorer();
        $this->databaseRestorer->dumpDB(__CLASS__);

        $this->installTestModule();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->databaseRestorer->restoreDB(__CLASS__);

        $this->cleanupTestData();

        parent::tearDown();
    }

    public function testModuleActivation(): void
    {
        $this->prepareTestModuleConfigurations(true, 1, []);

        $this->executeCommand($this->commandName);

        $moduleStateService = $this->get(ModuleStateServiceInterface::class);
        $this->assertTrue(
            $moduleStateService->isActive($this->moduleId, 1)
        );
    }

    public function testModuleDeactivation(): void
    {
        $this->get(ModuleActivationBridgeInterface::class)->activate($this->moduleId, 1);
        $this->prepareTestModuleConfigurations(false, 1, []);

        $this->executeCommand($this->commandName);

        $moduleStateService = $this->get(ModuleStateServiceInterface::class);
        $this->assertFalse(
            $moduleStateService->isActive($this->moduleId, 1)
        );
    }

    public function testModuleReactivation(): void
    {
        $this->get(ModuleActivationBridgeInterface::class)->activate($this->moduleId, 1);

        $moduleSetting = new Setting();
        $moduleSetting->setName('testSetting')->setValue(true);
        $this->prepareTestModuleConfigurations(true, 1, [$moduleSetting]);

        $this->executeCommand($this->commandName);

        $this->assertTrue(
            $this->get(ModuleStateServiceInterface::class)->isActive($this->moduleId, 1)
        );
    }

    public function testModuleActivationInAllShops(): void
    {
        $this->prepareTestModuleConfigurations(true, 1, []);
        $this->prepareTestModuleConfigurations(true, 2, []);

        $this->executeCommand($this->commandName);

        $moduleStateService = $this->get(ModuleStateServiceInterface::class);

        $this->assertTrue(
            $moduleStateService->isActive($this->moduleId, 1)
        );

        $this->assertTrue(
            $moduleStateService->isActive($this->moduleId, 2)
        );
    }

    private function prepareTestModuleConfigurations(bool $isConfigured, int $shopId, array $settings): void
    {
        $moduleToActivate = new ModuleConfiguration();
        $moduleToActivate
            ->setId($this->moduleId)
            ->setModuleSource(Path::join($this->modulesPath, $this->moduleId))
            ->setModuleSettings($settings)
            ->setConfigured($isConfigured);

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($moduleToActivate);

        $shopConfigurationDao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfigurationDao->save($shopConfiguration, $shopId);
    }
}
