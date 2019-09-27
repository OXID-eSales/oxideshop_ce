<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\ControllersModuleSettingHandler;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Configuration\DataObject\ShopSettingType;
use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;

/**
 * @internal
 */
class ControllersModuleSettingHandlerTest extends TestCase
{
    public function testHandleConvertsModuleIdsAndControllerKeysLowercase()
    {
        $shopConfigurationSettingDaoMock = $this->getShopConfigurationSettingDaoMock();

        $this->getShopConfigurationSettingWithExistingModuleControllers($shopConfigurationSettingDaoMock);

        $settingHandler = new ControllersModuleSettingHandler($shopConfigurationSettingDaoMock);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('newmodule')
            ->addController(
                new Controller(
                    'firstControllerNewModule',
                    \NewModule\FirstClass::class
                )
            )->addController(
                new Controller(
                    'secondControllerNewModule',
                    \NewModule\SecondClass::class
                )
            );

        $settingHandler->handleOnModuleActivation($moduleConfiguration, 1);

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

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('newmodule')
            ->addController(new Controller('', ''));

        $settingHandler->handleOnModuleActivation($moduleConfiguration, 1);

        $shopConfigurationSettingWithEmptyValue->setValue(['newmodule' => []]);

        $shopConfigurationSetting =
            $shopConfigurationSettingDaoMock->get(ShopConfigurationSetting::MODULE_CONTROLLERS, 1);

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
            ->setValue(['existingmodule' => ['controller' => 'someNamespace']]);

        $shopConfigurationSettingDaoMock
            ->method('get')
            ->willReturn($moduleControllers);

        $settingHandler = new ControllersModuleSettingHandler($shopConfigurationSettingDaoMock);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId('existingmodule');

        $settingHandler->handleOnModuleDeactivation($moduleConfiguration, 1);

        $this->assertSame(
            [],
            $shopConfigurationSettingDaoMock->get(ShopConfigurationSetting::MODULE_CONTROLLERS, 1)->getValue()
        );
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
