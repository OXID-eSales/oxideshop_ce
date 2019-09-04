<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Common\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge\ModuleConfigurationDaoBridge;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge\ModuleConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ShopEnvironmentConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ModuleConfiguration\ModuleSettingsDataMapper;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class ModuleConfigurationDaoBridgeTest extends TestCase
{
    use ContainerTrait;

    /**
     * @var string
     */
    private $testModuleId = 'testModuleId';

    public function testGet()
    {
        $context = $this->getMockBuilder(ContextInterface::class)->getMock();
        $context
            ->method('getCurrentShopId')
            ->willReturn(1789);

        $moduleConfigurationDao = $this->getMockBuilder(ModuleConfigurationDaoInterface::class)->getMock();
        $moduleConfigurationDao
            ->expects($this->once())
            ->method('get')
            ->with('testModuleId', 1789);

        $shopEnvironmentConfigurationDao =
            $this->getMockBuilder(ShopEnvironmentConfigurationDaoInterface::class)->getMock();

        $shopEnvironmentConfigurationDao->method('get')
            ->with(1);

        $bridge = new ModuleConfigurationDaoBridge($context, $moduleConfigurationDao, $shopEnvironmentConfigurationDao);
        $bridge->get('testModuleId');
    }

    public function testSave()
    {
        $context = $this->getMockBuilder(ContextInterface::class)->getMock();
        $context
            ->method('getCurrentShopId')
            ->willReturn(1799);

        $moduleConfiguration = new ModuleConfiguration();

        $moduleConfigurationDao = $this->getMockBuilder(ModuleConfigurationDaoInterface::class)->getMock();
        $moduleConfigurationDao
            ->expects($this->once())
            ->method('save')
            ->with($moduleConfiguration, 1799);


        $shopEnvironmentConfigurationDao =
            $this->getMockBuilder(ShopEnvironmentConfigurationDaoInterface::class)->getMock();

        $shopEnvironmentConfigurationDao->method('get')
            ->with(1);

        $bridge = new ModuleConfigurationDaoBridge($context, $moduleConfigurationDao, $shopEnvironmentConfigurationDao);
        $bridge->save($moduleConfiguration);
    }

    public function testSavingOverwritesValueFromEnvironmentShopConfigurationFile(): void
    {
        $moduleConfigurationDaoBridge = $this->get(ModuleConfigurationDaoBridgeInterface::class);

        $originalSetting = new Setting();
        $originalSetting
            ->setName('settingToOverwrite')
            ->setValue('originalValue')
            ->setType('int');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId($this->testModuleId)
            ->setPath('test')
            ->addModuleSetting($originalSetting);

        $moduleConfigurationDaoBridge->save($moduleConfiguration);

        $this->prepareTestEnvironmentShopConfigurationFile();

        $shopConfigurationDaoBridge = $this->get(ShopConfigurationDaoBridgeInterface::class);

        $this->assertSame(
            'overwrittenValue',
            $shopConfigurationDaoBridge
                ->get()
                ->getModuleConfiguration($this->testModuleId)
                ->getModuleSetting('settingToOverwrite')
                ->getValue()
        );
    }

    public function testSavingRemoveEnvironmentFile()
    {
        $moduleConfigurationDaoBridge = $this->get(ModuleConfigurationDaoBridgeInterface::class);

        $originalSetting = new Setting();
        $originalSetting
            ->setName('settingToOverwrite')
            ->setValue('originalValue')
            ->setType('int');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId($this->testModuleId)
            ->setPath('test')
            ->addModuleSetting($originalSetting);

        $moduleConfigurationDaoBridge->save($moduleConfiguration);

        $this->prepareTestEnvironmentShopConfigurationFile();
        $shopConfigurationDaoBridge = $this->get(ShopConfigurationDaoBridgeInterface::class);

        $this->assertSame(
            'overwrittenValue',
            $shopConfigurationDaoBridge
                ->get()
                ->getModuleConfiguration($this->testModuleId)
                ->getModuleSetting('settingToOverwrite')
                ->getValue()
        );

        $moduleConfigurationDaoBridge->save($moduleConfiguration);

        $this->assertSame(
            'originalValue',
            $shopConfigurationDaoBridge
                ->get()
                ->getModuleConfiguration($this->testModuleId)
                ->getModuleSetting('settingToOverwrite')
                ->getValue()
        );
    }

    /**
     * First create environment file and check if values are overwritten
     * Second save and see if environment file is removed and change to .bak
     * Third save again check if the environment file backup could be overwritten
     */
    public function testSavingOverwriteAlreadyBackupEnvironmentFile()
    {
        $moduleConfigurationDaoBridge = $this->get(ModuleConfigurationDaoBridgeInterface::class);

        $originalSetting = new Setting();
        $originalSetting
            ->setName('settingToOverwrite')
            ->setValue('originalValue')
            ->setType('int');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId($this->testModuleId)
            ->setPath('test')
            ->addModuleSetting($originalSetting);

        $moduleConfigurationDaoBridge->save($moduleConfiguration);

        $this->prepareTestEnvironmentShopConfigurationFile();
        $shopConfigurationDaoBridge = $this->get(ShopConfigurationDaoBridgeInterface::class);

        $this->assertSame(
            'overwrittenValue',
            $shopConfigurationDaoBridge
                ->get()
                ->getModuleConfiguration($this->testModuleId)
                ->getModuleSetting('settingToOverwrite')
                ->getValue()
        );

        $moduleConfigurationDaoBridge->save($moduleConfiguration);

        $this->assertSame(
            'originalValue',
            $shopConfigurationDaoBridge
                ->get()
                ->getModuleConfiguration($this->testModuleId)
                ->getModuleSetting('settingToOverwrite')
                ->getValue()
        );

        $this->prepareTestEnvironmentShopConfigurationFile();

        $moduleConfigurationDaoBridge->save($moduleConfiguration);

        $this->assertSame(
            'originalValue',
            $shopConfigurationDaoBridge
                ->get()
                ->getModuleConfiguration($this->testModuleId)
                ->getModuleSetting('settingToOverwrite')
                ->getValue()
        );
    }

    private function prepareTestEnvironmentShopConfigurationFile(): void
    {
        $fileStorageFactory = $this->get(FileStorageFactoryInterface::class);
        $storage = $fileStorageFactory->create(
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
