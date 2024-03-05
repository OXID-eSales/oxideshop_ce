<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Configuration\Dao;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleEnvironmentConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ModuleSettingsDataMapper;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ModuleEnvironmentConfigurationDaoTest extends TestCase
{
    use ContainerTrait;

    public function testGet(): void
    {
        $this->prepareTestEnvironmentShopConfigurationFile();
        $environmentConfiguration = $this->get(ModuleEnvironmentConfigurationDaoInterface::class)->get('testModuleId', 1);
        $expectedEnvironmentConfiguration = [
            ModuleSettingsDataMapper::MAPPING_KEY => [
                'settingToOverwrite' => [
                    'value' => 'overwrittenValue',
                ]
            ]
        ];

        $this->assertEquals($expectedEnvironmentConfiguration, $environmentConfiguration);
    }

    public function testRemove(): void
    {
        $this->prepareTestEnvironmentShopConfigurationFile();

        $this->get(ModuleEnvironmentConfigurationDaoInterface::class)->remove('testModuleId', 1);

        $environmentConfiguration = $this->get(ModuleEnvironmentConfigurationDaoInterface::class)->get('testModuleId', 1);

        $this->assertEquals([], $environmentConfiguration);
    }

    #[DoesNotPerformAssertions]
    public function testRemoveOverwriteAlreadyBackupEnvironmentFile(): void
    {
        $this->prepareTestEnvironmentShopConfigurationFile();
        $this->get(ModuleEnvironmentConfigurationDaoInterface::class)->remove('testModuleId', 1);

        $this->prepareTestEnvironmentShopConfigurationFile();
        $this->get(ModuleEnvironmentConfigurationDaoInterface::class)->remove('testModuleId', 1);
    }

    #[DoesNotPerformAssertions]
    public function testRemoveWithNonExistingEnvironmentFile(): void
    {
        $this->get(ModuleEnvironmentConfigurationDaoInterface::class)->remove('testModuleId', 1);
    }

    private function prepareTestEnvironmentShopConfigurationFile(): void
    {
        $fileStorageFactory = $this->get(FileStorageFactoryInterface::class);
        $storage = $fileStorageFactory->create(
            $this->get(ContextInterface::class)
                ->getProjectConfigurationDirectory() . 'environment/shops/1/modules/testModuleId.yaml'
        );

        $storage->save([
            ModuleSettingsDataMapper::MAPPING_KEY => [
                'settingToOverwrite' => [
                    'value' => 'overwrittenValue',
                ]
            ]
        ]);
    }
}
