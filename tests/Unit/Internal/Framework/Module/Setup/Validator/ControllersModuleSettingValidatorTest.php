<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\{
    Exception\ControllersDuplicationModuleConfigurationException,
    Validator\ControllersValidator,
};
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\{
    ModuleConfiguration,
    ModuleConfiguration\Controller,
};
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

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addController(
            new Controller(
                'newModuleControllerName',
                'newModuleControllerNamepace'
            )
        );

        $this->assertNull(
            $validator->validate($moduleConfiguration, 1)
        );
    }

    /**
     * @dataProvider duplicatedSettingValueDataProvider
     *
     * @param Controller[] $duplicatedSettingValue
     * @param bool $expectException
     *
     */
    public function testValidationWithDuplicatedControllerNamespace(
        array $duplicatedSettingValue,
        bool $expectException
    ) {
        if ($expectException) {
            $this->expectException(
                ControllersDuplicationModuleConfigurationException::class
            );
        }
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

        $moduleConfiguration = new ModuleConfiguration();
        foreach ($duplicatedSettingValue as $value) {
            $moduleConfiguration->addController(
                new Controller(
                    $value->getId(),
                    $value->getControllerClassNameSpace()
                )
            );
        }

        $validator->validate($moduleConfiguration, 1);

        if (!$expectException) {
            $this->assertTrue(true);
        }
    }

    public function duplicatedSettingValueDataProvider(): array
    {
        return [
            'same controller names with different classname' => [
                [
                    new Controller('moduleControllerName', 'duplicatedNamespace'),
                    new Controller('duplicatedName', 'moduleControllerNamepace'),
                ],
                'expectException?' => true
            ],
            'same controller names with same classname' => [
                [
                    new Controller('duplicatedname', 'duplicatedNamespace'),
                ],
                'expectException?' => false
            ],
        ];
    }
}
