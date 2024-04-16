<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\{
    ModuleConfiguration\ModuleSettingsDataMapper
};
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ShopConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ShopConfigurationDaoTest extends TestCase
{
    use ContainerTrait;

    private string $testModuleId = 'testModuleId';
    private string $testedSetting = 'settingToOverwrite';
    private string $originalValue = 'some-original-value';
    private string $newValue = 'some-new-value';

    public function testSave(): void
    {
        $shopConfigurationDao = $this->get(ShopConfigurationDaoInterface::class);

        $module = new ModuleConfiguration();
        $module
            ->setId('test')
            ->setModuleSource('test');

        $shopConfigurationWithModule = new ShopConfiguration();
        $shopConfigurationWithModule->addModuleConfiguration($module);
        $shopConfigurationDao->save($shopConfigurationWithModule, 1);

        $shopConfiguration = new ShopConfiguration();
        $shopConfigurationDao->save($shopConfiguration, 2);

        $this->assertEquals(
            $shopConfigurationWithModule,
            $shopConfigurationDao->get(1)
        );

        $this->assertEquals(
            $shopConfiguration,
            $shopConfigurationDao->get(2)
        );
    }

    public function testEnvironmentConfigurationOverwritesShopConfiguration(): void
    {
        $this->configureModuleInShopFile();
        $this->configureModuleInEnvironmentFile();

        $this->assertSame(
            $this->newValue,
            $this->get(ShopConfigurationDaoInterface::class)
                ->get(1)
                ->getModuleConfiguration($this->testModuleId)
                ->getModuleSetting($this->testedSetting)
                ->getValue()
        );
    }

    public function testGetAll(): void
    {
        $shopConfigurationDao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfigurationDao->save(new ShopConfiguration(), 1);

        $this->assertEquals(
            new ShopConfiguration(),
            $shopConfigurationDao->get(1)
        );

        $shopConfigurationDao->save(new ShopConfiguration(), 3);

        $this->assertEquals(
            [
                1 => new ShopConfiguration(),
                3 => new ShopConfiguration(),
            ],
            $shopConfigurationDao->getAll()
        );
    }

    public function testGetIncorrectShopId(): void
    {
        $this->expectException(ShopConfigurationNotFoundException::class);
        $shopConfigurationDao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfigurationDao->save(new ShopConfiguration(), 1);
        $shopConfigurationDao->save(new ShopConfiguration(), 2);
        $shopConfigurationDao->save(new ShopConfiguration(), 3);

        $this->expectException(ShopConfigurationNotFoundException::class);
        $shopConfigurationDao->get(99);
    }

    public function testGetCorrectShopId(): void
    {
        $shopConfigurationDao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfigurationDao->save(new ShopConfiguration(), 1);

        $shopConfiguration = $shopConfigurationDao->get(1);

        $this->assertEquals(
            $shopConfiguration,
            $shopConfigurationDao->get(1)
        );
    }

    public function testRemovingModuleConfiguration(): void
    {
        $shopConfigurationDao = $this->get(ShopConfigurationDaoInterface::class);

        $module = new ModuleConfiguration();
        $module
            ->setId('test')
            ->setModuleSource('test');

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($module);
        $shopConfigurationDao->save($shopConfiguration, 1);

        $shopConfiguration->deleteModuleConfiguration('test');

        $shopConfigurationDao->save($shopConfiguration, 1);

        $this->assertEquals([], $shopConfigurationDao->get(1)->getModuleConfigurations());
    }

    public function testDeleteAll(): void
    {
        $this->expectException(ShopConfigurationNotFoundException::class);
        $shopConfigurationDao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfigurationDao->save(new ShopConfiguration(), 1);
        $shopConfigurationDao->save(new ShopConfiguration(), 2);
        $shopConfigurationDao->save(new ShopConfiguration(), 3);

        $shopConfigurationDao->deleteAll();

        $this->expectException(ShopConfigurationNotFoundException::class);
        $this->assertEquals(
            [],
            $shopConfigurationDao->get(1)
        );
    }

    private function configureModuleInEnvironmentFile(): void
    {
        $storage = $this->get(FileStorageFactoryInterface::class)
            ->create(
                $this->get(ContextInterface::class)
                    ->getProjectConfigurationDirectory() . "environment/shops/1/modules/{$this->testModuleId}.yaml"
            );

        $storage->save([
            ModuleSettingsDataMapper::MAPPING_KEY => [
                $this->testedSetting => ['value' => $this->newValue],
            ]
        ]);
    }

    private function configureModuleInShopFile(): void
    {
        $originalModuleSetting = new Setting();
        $originalModuleSetting
            ->setName($this->testedSetting)
            ->setValue($this->originalValue)
            ->setType('int');
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId($this->testModuleId)
            ->setModuleSource('test')
            ->addModuleSetting($originalModuleSetting);
        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);
        $this->get(ShopConfigurationDaoInterface::class)->save($shopConfiguration, 1);
    }
}
