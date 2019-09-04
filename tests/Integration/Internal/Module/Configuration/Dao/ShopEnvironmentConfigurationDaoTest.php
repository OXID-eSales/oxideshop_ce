<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Common\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ShopEnvironmentConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ModuleConfiguration\ModuleSettingsDataMapper;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ShopEnvironmentConfigurationDaoTest extends TestCase
{
    use ContainerTrait;

    /**
     * @var string
     */
    private $testModuleId = 'testModuleId';

    public function testSave(): void
    {
        $shopConfigurationDao = $this->get(ShopConfigurationDaoInterface::class);

        $module = new ModuleConfiguration();
        $module
            ->setId('test')
            ->setPath('test');

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

    public function testRemoveWithNonExistingEnvironmentFile(): void
    {
        $this->get(ShopEnvironmentConfigurationDaoInterface::class)->remove(1);
    }

    public function testRemoveExistingEnvironmentFile(): void
    {
        $this->prepareTestEnvironmentShopConfigurationFile();
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
