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
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\ShopConfigurationEventsHandler;
use PHPUnit\Framework\TestCase;

final class ShopConfigurationEventsHandlerTest extends TestCase
{
    public function testHandleOnModuleActivation(): void
    {
        $shopConfigurationSettingDao = $this->getTestShopConfigurationSettingDao();

        $event = new ModuleConfiguration\Event('onActive', 'doSomething');
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('testId')
            ->setPath('testPath')
            ->addEvent($event);

        $handler = new ShopConfigurationEventsHandler($shopConfigurationSettingDao);
        $handler->handleOnModuleActivation($moduleConfiguration, 1);

        $this->assertSame(
            [
                'testId' => [
                    'onActive' => 'doSomething',
                ]
            ],
            $shopConfigurationSettingDao->get(ShopConfigurationSetting::MODULE_EVENTS, 1)->getValue()
        );
    }

    public function testHandleOnModuleDeactivation(): void
    {
        $shopConfigurationSettingDao = $this->getTestShopConfigurationSettingDao();

        $shopConfigurationSetting = new ShopConfigurationSetting();
        $shopConfigurationSetting
            ->setShopId(1)
            ->setName(ShopConfigurationSetting::MODULE_EVENTS)
            ->setType(ShopSettingType::ARRAY)
            ->setValue([
                'moduleToStayActive' => ['onActive' => 'doSomething'],
                'moduleToDeactivate' => ['onActive' => 'doSomethingElse'],
            ]);

        $shopConfigurationSettingDao->save($shopConfigurationSetting);

        $event = new ModuleConfiguration\Event('onActive', 'doSomething');
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('moduleToDeactivate')
            ->setPath('testPath')
            ->addEvent($event);

        $handler = new ShopConfigurationEventsHandler($shopConfigurationSettingDao);
        $handler->handleOnModuleDeactivation($moduleConfiguration, 1);

        $this->assertSame(
            ['moduleToStayActive' => ['onActive' => 'doSomething']],
            $shopConfigurationSettingDao->get(ShopConfigurationSetting::MODULE_EVENTS, 1)->getValue()
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
                    return $this->settings[$shopId][$name];
                }
                throw new EntryDoesNotExistDaoException();
            }

            public function delete(ShopConfigurationSetting $setting)
            {
                unset($this->settings[$setting->getShopId()][$setting->getName()]);
            }
        };
    }
}
