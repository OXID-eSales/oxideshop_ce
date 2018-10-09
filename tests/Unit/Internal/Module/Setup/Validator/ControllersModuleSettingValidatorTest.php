<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Validator\ControllersModuleSettingValidator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ControllersModuleSettingValidatorTest extends TestCase
{
    public function testCanValidate()
    {
        $validator = new ControllersModuleSettingValidator(
            $this->getMockBuilder(ShopAdapterInterface::class)->getMock(),
            $this->getMockBuilder(ShopConfigurationSettingDaoInterface::class)->getMock()
        );

        $controllersModuleSetting = new ModuleSetting('controllers', []);

        $this->assertTrue(
            $validator->canValidate($controllersModuleSetting)
        );
    }

    public function testCanNotValidate()
    {
        $validator = new ControllersModuleSettingValidator(
            $this->getMockBuilder(ShopAdapterInterface::class)->getMock(),
            $this->getMockBuilder(ShopConfigurationSettingDaoInterface::class)->getMock()
        );

        $notControllersModuleSetting = new ModuleSetting('notControllers', []);

        $this->assertFalse(
            $validator->canValidate($notControllersModuleSetting)
        );
    }

    public function testValidationWithCorrectSetting()
    {
        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();
        $shopAdapter
            ->method('getShopControllerClassMap')
            ->willReturn([
                'shopControllerName' => 'shopControllerNamespace',
            ]);

        $shopConfigurationSetting = new ShopConfigurationSetting();
        $shopConfigurationSetting->setValue(
            [
                'moduleId' => [
                    'alreadyActiveModuleControllerName' => 'alreadyActiveModuleControllerNamepace'
                ],
            ]
        );

        $shopConfigurationSettingDao = $this->getMockBuilder(ShopConfigurationSettingDaoInterface::class)->getMock();
        $shopConfigurationSettingDao
            ->method('get')
            ->willReturn($shopConfigurationSetting);

        $validator = new ControllersModuleSettingValidator($shopAdapter, $shopConfigurationSettingDao);

        $setting = new ModuleSetting('controllers', [
            'newModuleControllerName' => 'newModuleControllerNamepace',
        ]);

        $this->assertNull(
            $validator->validate($setting, 'someModuleId', 1)
        );
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSettingNotValidException
     *
     * @dataProvider duplicatedSettingValueDataProvider
     *
     * @param array
     */
    public function testValidationWithDuplicatedControllerNamespace($duplicatedSettingValue)
    {
        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();
        $shopAdapter
            ->method('getShopControllerClassMap')
            ->willReturn([
                'duplicatedname' => 'duplicatedNamespace',
            ]);

        $shopConfigurationSetting = new ShopConfigurationSetting();
        $shopConfigurationSetting->setValue(
            [
                'moduleId' => [
                    'alreadyActiveModuleControllerName' => 'alreadyActiveModuleControllerNamepace'
                ],
            ]
        );

        $shopConfigurationSettingDao = $this->getMockBuilder(ShopConfigurationSettingDaoInterface::class)->getMock();
        $shopConfigurationSettingDao
            ->method('get')
            ->willReturn($shopConfigurationSetting);

        $validator = new ControllersModuleSettingValidator($shopAdapter, $shopConfigurationSettingDao);

        $setting = new ModuleSetting('controllers', $duplicatedSettingValue);

        $validator->validate($setting, 'someModuleId', 1);
    }

    public function duplicatedSettingValueDataProvider(): array
    {
        return [
            [
                ['moduleControllerName' => 'duplicatedNamespace'],
            ],
            [
                ['duplicatedName' => 'moduleControllerNamepace'],
            ],
        ];
    }
}
