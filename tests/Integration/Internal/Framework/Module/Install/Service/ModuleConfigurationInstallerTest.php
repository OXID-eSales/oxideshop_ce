<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Install\Service;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\{
    ModuleConfiguration\ModuleSettingsDataMapper};
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ModuleConfigurationInstallerTest extends TestCase
{
    use ContainerTrait;

    /** @var string  */
    private $modulePath;
    /**
     * @see TestData/TestModule/metadata.php
     */
    private string $testModuleId = 'test-module';
    /** @var ProjectConfigurationDaoInterface */
    private $projectConfigurationDao;

    public function setUp(): void
    {
        $this->modulePath = realpath(__DIR__ . '/../../TestData/TestModule/');

        $this->projectConfigurationDao = $this->get(ProjectConfigurationDaoInterface::class);

        $this->prepareTestProjectConfiguration();

        parent::setUp();
    }

    public function testInstall(): void
    {
        $configurationInstaller = $this->get(ModuleConfigurationInstallerInterface::class);
        $configurationInstaller->install($this->modulePath);

        $this->assertProjectConfigurationHasModuleConfigurationForAllShops();
    }

    #[DoesNotPerformAssertions]
    public function testInstallWithPreExistingEnvironmentFile(): void
    {
        $this->configureModuleInEnvironmentFile();
        $configurationInstaller = $this->get(ModuleConfigurationInstallerInterface::class);
        $configurationInstaller->install($this->modulePath);
    }

    public function testInstallWithTwoShopsWillKeepSeparateModuleConfigurationsPerShop(): void
    {
        $shopId1 = 1;
        $shopId2 = 2;
        $settingValueShop1 = 'firstShopSetting';
        $settingValueShop2 = 'secondShopSetting';
        $testedSettingName = 'string-setting';

        $configurationInstaller = $this->get(ModuleConfigurationInstallerInterface::class);

        $configurationInstaller->install($this->modulePath);
        $moduleConfigurationDao = $this->get(ModuleConfigurationDaoInterface::class);

        $moduleConfigurationShop1 = $moduleConfigurationDao->get($this->testModuleId, $shopId1);
        $testedSettingShop1 = $moduleConfigurationShop1->getModuleSetting($testedSettingName);
        $testedSettingShop1->setValue($settingValueShop1);
        $moduleConfigurationDao->save($moduleConfigurationShop1, $shopId1);

        $moduleConfigurationShop2 = $moduleConfigurationDao->get($this->testModuleId, $shopId2);
        $testedSettingShop2 = $moduleConfigurationShop2->getModuleSetting($testedSettingName);
        $testedSettingShop2->setValue($settingValueShop2);
        $moduleConfigurationDao->save($moduleConfigurationShop2, $shopId2);

        $configurationInstaller->install($this->modulePath);

        $actualSettingValueShop1 = $moduleConfigurationDao->get($this->testModuleId, $shopId1)
            ->getModuleSetting($testedSettingName)
            ->getValue();
        $actualSettingValueShop2 = $moduleConfigurationDao->get($this->testModuleId, $shopId2)
            ->getModuleSetting($testedSettingName)
            ->getValue();

        $this->assertSame($settingValueShop1, $actualSettingValueShop1);
        $this->assertSame($settingValueShop2, $actualSettingValueShop2);
    }

    public function testUninstall(): void
    {
        $configurationInstaller = $this->get(ModuleConfigurationInstallerInterface::class);
        $configurationInstaller->install($this->modulePath);

        $configurationInstaller->uninstall($this->modulePath);

        $this->assertModuleConfigurationDeletedForAllShops();
    }

    public function testUninstallById(): void
    {
        $configurationInstaller = $this->get(ModuleConfigurationInstallerInterface::class);
        $configurationInstaller->install($this->modulePath);

        $configurationInstaller->uninstallById($this->testModuleId);

        $this->assertModuleConfigurationDeletedForAllShops();
    }

    public function testIsInstalled(): void
    {
        $moduleConfigurationInstaller = $this->get(ModuleConfigurationInstallerInterface::class);

        $this->assertFalse(
            $moduleConfigurationInstaller->isInstalled($this->modulePath)
        );

        $moduleConfigurationInstaller->install($this->modulePath);

        $this->assertTrue(
            $moduleConfigurationInstaller->isInstalled($this->modulePath)
        );
    }

    public function testRelativeModuleSourcePathIsSetToModuleConfigurations(): void
    {
        $moduleConfigurationInstaller = $this->get(ModuleConfigurationInstallerInterface::class);
        $moduleConfigurationInstaller->install($this->modulePath);

        $shopConfiguration = $this
            ->projectConfigurationDao
            ->getConfiguration()
            ->getShopConfiguration(1);

        $this->assertStringContainsString(
            $shopConfiguration->getModuleConfiguration($this->testModuleId)->getModuleSource(),
            $this->modulePath
        );
    }

    private function assertProjectConfigurationHasModuleConfigurationForAllShops(): void
    {
        $environmentConfiguration = $this
            ->projectConfigurationDao
            ->getConfiguration();

        foreach ($environmentConfiguration->getShopConfigurations() as $shopConfiguration) {
            $this->assertContains(
                $this->testModuleId,
                $shopConfiguration->getModuleIdsOfModuleConfigurations()
            );
        }
    }

    private function assertModuleConfigurationDeletedForAllShops(): void
    {
        foreach ($this->get(ShopConfigurationDaoInterface::class)->getAll() as $shopConfiguration) {
            $this->assertFalse($shopConfiguration->hasModuleConfiguration($this->testModuleId));
        }
    }

    private function prepareTestProjectConfiguration(): void
    {
        $shopConfigurationWithChain = new ShopConfiguration();

        $chain = new ClassExtensionsChain();
        $chain->setChain([
            'shopClass'             => ['alreadyInstalledShopClass', 'anotherAlreadyInstalledShopClass'],
            'someAnotherShopClass'  => ['alreadyInstalledShopClass'],
        ]);

        $shopConfigurationWithChain->setClassExtensionsChain($chain);

        $shopConfigurationWithoutChain = new ShopConfiguration();

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->addShopConfiguration(1, $shopConfigurationWithChain);
        $projectConfiguration->addShopConfiguration(2, $shopConfigurationWithoutChain);

        $this->projectConfigurationDao->save($projectConfiguration);
    }

    private function configureModuleInEnvironmentFile(): void
    {
        $storage = $this->get(FileStorageFactoryInterface::class)
            ->create(
                $this->get(ContextInterface::class)
                    ->getProjectConfigurationDirectory() . 'environment/1.yaml'
            );

        $storage->save([
            'modules' => [
                $this->testModuleId => [
                    ModuleSettingsDataMapper::MAPPING_KEY => [
                        'settingToOverwrite' => [
                            'value' => 'overwrittenValue',
                        ]
                    ]
                ]
            ]
        ]);
    }
}
