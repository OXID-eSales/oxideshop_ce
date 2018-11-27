<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Handler\ControllersModuleSettingHandler;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopSettingType;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ControllersModuleSettingHandlerTest extends TestCase
{
    public function testCanHandleSetting()
    {
        $settingHandler = new ControllersModuleSettingHandler($this->getShopConfigurationSettingDaoMock());
        $moduleSetting = new ModuleSetting(ModuleSetting::CONTROLLERS, []);

        $this->assertTrue(
            $settingHandler->canHandle($moduleSetting)
        );
    }

    public function testCanNotHandleSetting()
    {
        $settingHandler = new ControllersModuleSettingHandler($this->getShopConfigurationSettingDaoMock());
        $moduleSetting = new ModuleSetting('anotherSetting', []);

        $this->assertFalse(
            $settingHandler->canHandle($moduleSetting)
        );
    }

    public function testHandleConvertsModuleIdsAndControllerKeysLowercase()
    {
        $shopConfigurationSettingDaoMock = $this->getShopConfigurationSettingDaoMock();

        $this->getShopConfigurationSettingWithExistingModuleControllers($shopConfigurationSettingDaoMock);

        $settingHandler = new ControllersModuleSettingHandler($shopConfigurationSettingDaoMock);

        $moduleSetting = new ModuleSetting(
            ModuleSetting::CONTROLLERS,
            [
                    'firstControllerNewModule'  => \NewModule\FirstClass::class,
                    'secondControllerNewModule' => \NewModule\SecondClass::class,
            ]
        );

        $settingHandler->handleOnModuleActivation($moduleSetting, 'newModule', 1);

        $this->assertSame(
            [
                'existingmodule' => [
                    'firstcontrollerexistingmodule'  => \OldModule\FirstControllerClass::class
                ],
                'newmodule' => [
                    'firstcontrollernewmodule'  => \NewModule\FirstClass::class,
                    'secondcontrollernewmodule' => \NewModule\SecondClass::class,
                ]
            ],
            $shopConfigurationSettingDaoMock->get(ShopConfigurationSetting::MODULE_CONTROLLERS, 1)->getValue()
        );
    }

    public function testHandleSavesEmptyShopConfigurationSettingIfNoControllersFound()
    {
        $shopConfigurationSettingDaoMock = $this->getShopConfigurationSettingDaoMock();

        $shopConfigurationSettingWithEmptyValue = $this->getShopConfigurationSettingWithEmptyValue();

        $shopConfigurationSettingDaoMock->method('get')->willReturn(
            $shopConfigurationSettingWithEmptyValue
        );

        $settingHandler = new ControllersModuleSettingHandler($shopConfigurationSettingDaoMock);

        $moduleSetting = new ModuleSetting(ModuleSetting::CONTROLLERS, []);
        $settingHandler->handleOnModuleActivation($moduleSetting, 'newModule', 1);

        $shopConfigurationSettingWithEmptyValue->setValue(['newmodule' => []]);

        $shopConfigurationSetting = $shopConfigurationSettingDaoMock->get(ShopConfigurationSetting::MODULE_CONTROLLERS, 1);

        $this->assertEquals(
            $shopConfigurationSettingWithEmptyValue,
            $shopConfigurationSetting
        );
        $this->assertSame(
            $shopConfigurationSettingWithEmptyValue->getValue(),
            $shopConfigurationSetting->getValue()
        );
    }

    public function testHandlingOnModuleDeactivation()
    {
        $shopConfigurationSettingDaoMock = $this->getShopConfigurationSettingDaoMock();

        $moduleControllers = new ShopConfigurationSetting();
        $moduleControllers
            ->setName(ShopConfigurationSetting::MODULE_CONTROLLERS)
            ->setShopId(1)
            ->setType(ShopSettingType::ARRAY)
            ->setValue(['existingModule' => ['controller' => 'someNamespace']]);

        $shopConfigurationSettingDaoMock
            ->method('get')
            ->willReturn($moduleControllers);

        $settingHandler = new ControllersModuleSettingHandler($shopConfigurationSettingDaoMock);

        $settingHandler->handleOnModuleDeactivation(
            new ModuleSetting(ModuleSetting::CONTROLLERS, []),
            'existingModule',
            1
        );

        $this->assertSame(
            [],
            $shopConfigurationSettingDaoMock->get(ShopConfigurationSetting::MODULE_CONTROLLERS, 1)->getValue()
        );
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\WrongModuleSettingException
     */
    public function testHandleWrongSettingOnModuleDeactivation()
    {
        $shopConfigurationSettingDaoMock = $this->getShopConfigurationSettingDaoMock();

        $settingHandler = new ControllersModuleSettingHandler($shopConfigurationSettingDaoMock);

        $moduleSetting = new ModuleSetting('notControllesSetting', []);

        $settingHandler->handleOnModuleDeactivation($moduleSetting, 'existingmodule', 1);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|ShopConfigurationSettingDaoInterface
     */
    private function getShopConfigurationSettingDaoMock(): ShopConfigurationSettingDaoInterface
    {
        return $this
            ->getMockBuilder(ShopConfigurationSettingDaoInterface::class)
            ->getMock();
    }

    /**
     * @return ShopConfigurationSetting
     */
    private function getShopConfigurationSettingWithEmptyValue(): ShopConfigurationSetting
    {
        $moduleControllers = new ShopConfigurationSetting();
        $moduleControllers
            ->setName(ShopConfigurationSetting::MODULE_CONTROLLERS)
            ->setShopId(1)
            ->setType(ShopSettingType::ARRAY)
            ->setValue([]);

        return $moduleControllers;
    }

    /**
     * @param $shopConfigurationSettingDaoMock
     */
    private function getShopConfigurationSettingWithExistingModuleControllers($shopConfigurationSettingDaoMock)
    {
        $moduleControllers = new ShopConfigurationSetting();
        $moduleControllers
            ->setName(ShopConfigurationSetting::MODULE_CONTROLLERS)
            ->setShopId(1)
            ->setType(ShopSettingType::ARRAY)
            ->setValue(['existingmodule' => ['firstcontrollerexistingmodule' => \OldModule\FirstControllerClass::class]]);

        $shopConfigurationSettingDaoMock->method('get')->willReturn(
            $moduleControllers
        );
    }
}
