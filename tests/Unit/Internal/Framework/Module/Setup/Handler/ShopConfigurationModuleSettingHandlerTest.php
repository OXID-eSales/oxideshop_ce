<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\ShopConfigurationModuleSettingHandler;
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

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModule');

        $setting = new Setting();
        $setting
            ->setName('moduleSetting')
            ->setValue('testModulePath');

        $moduleConfiguration->addModuleSetting($setting);

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

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('moduleToDeactivate');
        $setting = new Setting();
        $setting
            ->setName('moduleSetting')
            ->setValue('value');

        $moduleConfiguration->addModuleSetting($setting);

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
