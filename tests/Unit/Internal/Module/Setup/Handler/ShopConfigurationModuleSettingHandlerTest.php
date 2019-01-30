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
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Handler\ShopConfigurationModuleSettingHandler;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ShopConfigurationModuleSettingHandlerTest extends TestCase
{
    public function testHandleSettingOnModuleActivation()
    {
        $shopConfigurationSetting = new ShopConfigurationSetting();
        $shopConfigurationSetting
            ->setShopId(1)
            ->setName('shopSetting')
            ->setType(ShopSettingType::ARRAY)
            ->setValue(['alreadyExistentModuleId' => 'alreadyExistentValue']);

        $shopConfigurationSettingDao = $this->getTestShopConfigurationSettingDao();
        $shopConfigurationSettingDao->save($shopConfigurationSetting);

        $settingHandler = new ShopConfigurationModuleSettingHandler(
            'moduleSetting',
            'shopSetting',
            $shopConfigurationSettingDao
        );
        $moduleSetting = new ModuleSetting('moduleSetting', 'testModulePath');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModule');
        $moduleConfiguration->addSetting($moduleSetting);

        $settingHandler->handleOnModuleActivation($moduleConfiguration, 1);

        $shopConfigurationSetting = $shopConfigurationSettingDao->get('shopSetting', 1);

        $this->assertEquals(
            [
                'alreadyExistentModuleId' => 'alreadyExistentValue',
                'testModule'              => 'testModulePath',
            ],
            $shopConfigurationSetting->getValue()
        );
    }

    public function testHandleSettingOnModuleDeactivation()
    {
        $shopConfigurationSetting = new ShopConfigurationSetting();
        $shopConfigurationSetting
            ->setShopId(1)
            ->setName('shopSetting')
            ->setType(ShopSettingType::ARRAY)
            ->setValue([
                'moduleToStayActive' => 'value',
                'moduleToDeactivate' => 'value'
            ]);

        $shopConfigurationSettingDao = $this->getTestShopConfigurationSettingDao();
        $shopConfigurationSettingDao->save($shopConfigurationSetting);

        $settingHandler = new ShopConfigurationModuleSettingHandler(
            'moduleSetting',
            'shopSetting',
            $shopConfigurationSettingDao
        );
        $moduleSetting = new ModuleSetting('moduleSetting', 'value');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('moduleToDeactivate');
        $moduleConfiguration->addSetting($moduleSetting);

        $settingHandler->handleOnModuleDeactivation($moduleConfiguration, 1);

        $this->assertEquals(
            [
                'moduleToStayActive' => 'value',
            ],
            $shopConfigurationSettingDao->get('shopSetting', 1)->getValue()
        );
    }

    private function getTestShopConfigurationSettingDao(): ShopConfigurationSettingDaoInterface
    {
        return new class implements ShopConfigurationSettingDaoInterface {
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
