<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\ModuleVersionHandler;
use PHPUnit\Framework\TestCase;

final class ModuleVersionHandlerTest extends TestCase
{
    public function testHandleOnModuleActivation(): void
    {
        $shopConfigurationSettingDao = $this->getTestShopConfigurationSettingDao();

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testId')
            ->setPath('testPath')
            ->setVersion('0.0.1');

        $handler = new ModuleVersionHandler($shopConfigurationSettingDao);
        $handler->handleOnModuleActivation($moduleConfiguration, 1);

        $this->assertSame(
            ['testId' => '0.0.1'],
            $shopConfigurationSettingDao->get(ShopConfigurationSetting::MODULE_VERSIONS, 1)->getValue()
        );
    }

    public function testHandleOnModuleDeactivation(): void
    {
        $shopConfigurationSettingDao = $this->getTestShopConfigurationSettingDao();

        $shopConfigurationSetting = new ShopConfigurationSetting();
        $shopConfigurationSetting
            ->setShopId(1)
            ->setName(ShopConfigurationSetting::MODULE_VERSIONS)
            ->setType(ShopSettingType::ARRAY)
            ->setValue([
                'moduleToStayActive' => '0.0.1',
                'moduleToDeactivate' => '0.0.2'
            ]);

        $shopConfigurationSettingDao->save($shopConfigurationSetting);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('moduleToDeactivate')
            ->setPath('testPath')
            ->setVersion('0.0.2');

        $handler = new ModuleVersionHandler($shopConfigurationSettingDao);
        $handler->handleOnModuleDeactivation($moduleConfiguration, 1);

        $this->assertSame(
            ['moduleToStayActive' => '0.0.1'],
            $shopConfigurationSettingDao->get(ShopConfigurationSetting::MODULE_VERSIONS, 1)->getValue()
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
                if (isset($this->settings[$shopId][$name])) {
                    $setting = $this->settings[$shopId][$name];
                } else {
                    $setting = new ShopConfigurationSetting();
                    $setting
                        ->setShopId(1)
                        ->setName($name)
                        ->setType(ShopSettingType::ARRAY)
                        ->setValue([]);
                }

                return $setting;
            }

            public function delete(ShopConfigurationSetting $setting)
            {
                unset($this->settings[$setting->getShopId()][$setting->getName()]);
            }
        };
    }
}
