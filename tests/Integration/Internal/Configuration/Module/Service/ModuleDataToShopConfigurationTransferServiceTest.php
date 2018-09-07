<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Configuration\Module\Service;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\ModuleConfigurationToShopConfigurationDataMapper;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\Service\ModuleDataToShopConfigurationTransferService;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleDataToShopConfigurationTransferServiceTest extends TestCase
{
    public function testDataTransfer()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testModule')
            ->setPath('testModulePath')
            ->setVersion('v2.0');

        $shopConfigurationSettingDao = $this->getTestShopConfigurationSettingDao();
        $shopConfigurationSettingDao->save(new ShopConfigurationSetting(
                1,
                'aModulePaths',
                ['alreadyExistedModuleId' => 'alreadyExistedModulePath']
            )
        );

        $moduleDataToShopConfigurationTransferService = new ModuleDataToShopConfigurationTransferService(
            new ModuleConfigurationToShopConfigurationDataMapper(),
            $shopConfigurationSettingDao
        );

        $moduleDataToShopConfigurationTransferService->transfer($moduleConfiguration, 1);

        $shopConfigurationSetting = $shopConfigurationSettingDao->get('aModulePaths', 1);

        $this->assertEquals(
            [
                'alreadyExistedModuleId' => 'alreadyExistedModulePath',
                'testModule'             => 'testModulePath',
            ],
            $shopConfigurationSetting->getValue()
        );
    }

    private function getTestShopConfigurationSettingDao(): ShopConfigurationSettingDaoInterface
    {
        return new class implements ShopConfigurationSettingDaoInterface
        {
            private $settings = [];

            public function save(ShopConfigurationSetting $setting)
            {
                $this->settings[$setting->getShopId()][$setting->getName()] = $setting;
            }

            public function get(string $name, int $shopId): ShopConfigurationSetting
            {
                return $this->settings[$shopId][$name] ?? new ShopConfigurationSetting($shopId, $name, []);
            }
        };
    }
}
