<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopEnvironmentConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ModuleSettingsDataMapper;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ShopEnvironmentConfigurationDaoTest extends TestCase
{
    use ContainerTrait;

    public function testGet(): void
    {
        $this->prepareTestEnvironmentShopConfigurationFile();
        $environmentConfiguration = $this->get(ShopEnvironmentConfigurationDaoInterface::class)->get(1);
        $expectedEnvironmentConfiguration = [
            'modules' => [
                'testModuleId' => [
                    ModuleSettingsDataMapper::MAPPING_KEY => [
                        'settingToOverwrite' => [
                            'value' => 'overwrittenValue',
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals($expectedEnvironmentConfiguration, $environmentConfiguration);
    }

    public function testRemove(): void
    {
        $this->prepareTestEnvironmentShopConfigurationFile();

        $this->get(ShopEnvironmentConfigurationDaoInterface::class)->remove(1);

        $environmentConfiguration = $this->get(ShopEnvironmentConfigurationDaoInterface::class)->get(1);

        $this->assertEquals([], $environmentConfiguration);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testRemoveOverwriteAlreadyBackupEnvironmentFile(): void
    {
        $this->prepareTestEnvironmentShopConfigurationFile();
        $this->get(ShopEnvironmentConfigurationDaoInterface::class)->remove(1);

        $this->prepareTestEnvironmentShopConfigurationFile();
        $this->get(ShopEnvironmentConfigurationDaoInterface::class)->remove(1);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testRemoveWithNonExistingEnvironmentFile(): void
    {
        $this->get(ShopEnvironmentConfigurationDaoInterface::class)->remove(1);
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
                'testModuleId' => [
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
