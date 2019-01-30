<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Handler\ModulePathHandler;
use PHPUnit\Framework\TestCase;

class ModulePathHandlerTest extends TestCase
{
    public function testHandleOnModuleActivation()
    {
        $shopConfigurationSettingDao = $this->getTestShopConfigurationSettingDao();

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testId')
            ->setPath('testPath');

        $handler = new ModulePathHandler($shopConfigurationSettingDao);
        $handler->handleOnModuleActivation($moduleConfiguration, 1);

        $this->assertSame(
            ['testId' => 'testPath'],
            $shopConfigurationSettingDao->get(ShopConfigurationSetting::MODULE_PATHS, 1)->getValue()
        );
    }

    public function testHandleOnModuleDeactivation()
    {
        $shopConfigurationSettingDao = $this->getTestShopConfigurationSettingDao();

        $shopConfigurationSetting = new ShopConfigurationSetting();
        $shopConfigurationSetting
            ->setShopId(1)
            ->setName(ShopConfigurationSetting::MODULE_PATHS)
            ->setType(ShopSettingType::ARRAY)
            ->setValue([
                'moduleToStayActive' => 'path',
                'moduleToDeactivate' => 'path'
            ]);

        $shopConfigurationSettingDao->save($shopConfigurationSetting);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('moduleToDeactivate')
            ->setPath('testPath');

        $handler = new ModulePathHandler($shopConfigurationSettingDao);
        $handler->handleOnModuleDeactivation($moduleConfiguration, 1);

        $this->assertSame(
            ['moduleToStayActive' => 'path'],
            $shopConfigurationSettingDao->get(ShopConfigurationSetting::MODULE_PATHS, 1)->getValue()
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
