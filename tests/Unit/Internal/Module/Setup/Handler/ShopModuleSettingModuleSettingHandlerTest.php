<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Module\ShopModuleSetting\ShopModuleSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\ShopModuleSetting\ShopModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Handler\ShopModuleSettingModuleSettingHandler;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ShopModuleSettingModuleSettingHandlerTest extends TestCase
{
    public function testCanHandle()
    {
        $handler = new ShopModuleSettingModuleSettingHandler(
            $this->getMockBuilder(ShopModuleSettingDaoInterface::class)->getMock()
        );

        $moduleSetting = new ModuleSetting(ModuleSetting::SHOP_MODULE_SETTING, []);

        $this->assertTrue(
            $handler->canHandle($moduleSetting)
        );
    }

    public function testCanNotHandle()
    {
        $handler = new ShopModuleSettingModuleSettingHandler(
            $this->getMockBuilder(ShopModuleSettingDaoInterface::class)->getMock()
        );

        $moduleSetting = new ModuleSetting('anotherSetting', []);

        $this->assertFalse(
            $handler->canHandle($moduleSetting)
        );
    }

    public function testHandling()
    {
        $moduleSetting = new ModuleSetting(
            ModuleSetting::SHOP_MODULE_SETTING,
            [
                [
                    'name'          => 'blCustomGridFramework',
                    'type'          => 'bool',
                    'value'         => 'false',
                    'constraints'   => '1|2|3',
                    'group'         => 'frontend',
                    'position'      => 5,
                ],
            ]
        );

        $shopModuleSetting = new ShopModuleSetting();
        $shopModuleSetting
            ->setName('blCustomGridFramework')
            ->setValue('false')
            ->setType('bool')
            ->setShopId(1)
            ->setModuleId('testModule')
            ->setConstraints(['1', '2', '3',])
            ->setGroupName('frontend')
            ->setPositionInGroup(5);

        $shopModuleSettingDao = $this->getMockBuilder(ShopModuleSettingDaoInterface::class)->getMock();
        $shopModuleSettingDao
            ->expects($this->atLeastOnce())
            ->method('save')
            ->with($shopModuleSetting);

        $handler = new ShopModuleSettingModuleSettingHandler($shopModuleSettingDao);
        $handler->handle($moduleSetting, 'testModule', 1);
    }
}
