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

    public function testHandlingOnModuleActivation()
    {
        $moduleSetting = $this->getTestModuleSetting();
        $shopModuleSetting = $this->getTestShopModuleSetting();

        $shopModuleSettingDao = $this->getMockBuilder(ShopModuleSettingDaoInterface::class)->getMock();
        $shopModuleSettingDao
            ->expects($this->once())
            ->method('save')
            ->with($shopModuleSetting);

        $handler = new ShopModuleSettingModuleSettingHandler($shopModuleSettingDao);
        $handler->handleOnModuleActivation($moduleSetting, 'testModule', 1);
    }

    public function testHandlingOnModuleDeactivation()
    {
        $moduleSetting = $this->getTestModuleSetting();
        $shopModuleSetting = $this->getTestShopModuleSetting();

        $shopModuleSettingDao = $this->getMockBuilder(ShopModuleSettingDaoInterface::class)->getMock();
        $shopModuleSettingDao
            ->expects($this->once())
            ->method('delete')
            ->with($shopModuleSetting);

        $handler = new ShopModuleSettingModuleSettingHandler($shopModuleSettingDao);
        $handler->handleOnModuleDeactivation($moduleSetting, 'testModule', 1);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\WrongModuleSettingException
     */
    public function testHandleWrongSettingOnModuleActivation()
    {
        $handler = new ShopModuleSettingModuleSettingHandler(
            $this->getMockBuilder(ShopModuleSettingDaoInterface::class)->getMock()
        );

        $handler->handleOnModuleActivation(
            new ModuleSetting('wrongSettingForThisHandler', []),
            'testModule',
            1
        );
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\WrongModuleSettingException
     */
    public function testHandleWrongSettingOnModuleDeactivation()
    {
        $handler = new ShopModuleSettingModuleSettingHandler(
            $this->getMockBuilder(ShopModuleSettingDaoInterface::class)->getMock()
        );

        $handler->handleOnModuleActivation(
            new ModuleSetting('wrongSettingForThisHandler', []),
            'testModule',
            1
        );
    }

    private function getTestModuleSetting(): ModuleSetting
    {
        return new ModuleSetting(
            ModuleSetting::SHOP_MODULE_SETTING,
            [
                [
                    'name'          => 'blCustomGridFramework',
                    'type'          => 'bool',
                    'value'         => 'false',
                    'constraints'   => ['1', '2', '3',],
                    'group'         => 'frontend',
                    'position'      => 5,
                ],
            ]
        );
    }

    private function getTestShopModuleSetting(): ShopModuleSetting
    {
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

        return $shopModuleSetting;
    }
}
