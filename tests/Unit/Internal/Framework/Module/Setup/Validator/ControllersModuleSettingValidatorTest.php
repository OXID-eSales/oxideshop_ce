<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ControllersValidator;
use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;

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
     * @expectedException \OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ControllersDuplicationModuleConfigurationException
     *
     * @dataProvider duplicatedSettingValueDataProvider
     *
     * @param Controller[] $duplicatedSettingValue
     *
     */
    public function testValidationWithDuplicatedControllerNamespace(array $duplicatedSettingValue)
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
    }

    public function duplicatedSettingValueDataProvider(): array
    {
        return [
            [
                [
                    new Controller('moduleControllerName', 'duplicatedNamespace'),
                ],
                [
                    new Controller('duplicatedName', 'moduleControllerNamepace'),
                ],
            ]
        ];
    }
}
