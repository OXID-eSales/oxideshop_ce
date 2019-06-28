<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Validator\ControllersValidator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ControllersModuleSettingValidatorTest extends TestCase
{
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

        $validator = new ControllersValidator($shopAdapter, $shopConfigurationSettingDao);

        $setting = new ModuleSetting('controllers', [
            'newModuleControllerName' => 'newModuleControllerNamepace',
        ]);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSetting($setting);

        $this->assertNull(
            $validator->validate($moduleConfiguration,  1)
        );
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ControllersDuplicationModuleConfigurationException
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

        $validator = new ControllersValidator($shopAdapter, $shopConfigurationSettingDao);

        $setting = new ModuleSetting('controllers', $duplicatedSettingValue);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSetting($setting);

        $validator->validate($moduleConfiguration, 1);
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
